<?php

namespace OCI8Repository\Statement;

use Doctrine\DBAL\Connection;
use OCI8Repository\Statement\OCI8ProcedureStatement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use OCI8Repository\Parameter\ProcParametersInterface;
use OCI8Repository\Parameter\ProcParameter;

/**
 * Class that allows OCI8BaseRepository to build a Statement compatible with an Oracle procedure
 * @author Gino Mazzola <Gino.MAZZOLA@ext.ec.europa.eu>
 */
class OCI8ProcedureStatementPrepare {

    private $current_statement;
    private $connection;
    private $repository;
    private $cursors;

    public function __construct(ServiceEntityRepository $repository, Connection $connection, string $query) {

        $this->connection = $connection;
        $this->current_statement = $this->connection->prepare("BEGIN " . $query . "; END;");
        $this->repository = $repository;
        $this->cursors = [];
    }

    public function bindValue(string $param, $value, $type = ParameterType::STRING) {

        if ($this->current_statement) {
            $this->current_statement->bindValue($param, $value, $type);
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
        return $this;
    }

    public function addCursor(string $param) {
        $this->cursors[$param] = null;
        return $this;
    }

    public function bindParam(string $param, $value, $type = ParameterType::STRING, $length = null) {

        if ($this->current_statement) {
            $this->current_statement->bindParam($param, $value, $type, $length);
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
        return $this;
    }

    public function bindOut(string $param, string &$out, $type = ParameterType::STRING, $length = null) {

        if ($this->current_statement) {
            $this->current_statement->bindParam($param, $out, $type, $length);
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
        return $this;
    }

    public function with(ProcParametersInterface $parameters, array &$outs) {

        /* @var $procParam \App\Parameter\ProcParameter */
        foreach ($parameters->toArray() as $procParam) {

            if ($procParam->getBind() === ProcParameter::PARAM_IN) {
                $this->bindValue(
                        $procParam->getName(),
                        $procParam->getValue(),
                        $procParam->getType()
                );
            }

            if ($procParam->getBind() === ProcParameter::PARAM_OUT) {

                $outs[$procParam->getName()] = $this->getOutValue();

                $this->bindOut(
                        $procParam->getName(),
                        $outs[$procParam->getName()]
                );
            }

            if ($procParam->getBind() === ProcParameter::PARAM_CURSOR) {
                $this->addCursor(
                        $procParam->getName()
                );
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getOutValue() {
        return uniqid();
    }

    public function prepare(): OCI8ProcedureStatement {
        return new OCI8ProcedureStatement($this->cursors, $this->current_statement, $this->repository);
    }

}
