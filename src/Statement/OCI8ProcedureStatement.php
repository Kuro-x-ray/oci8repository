<?php

namespace OCI8Repository\Statement;

use Doctrine\DBAL\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EcPhp\DoctrineOci8\Doctrine\DBAL\Driver\OCI8\OCI8;

class OCI8ProcedureStatement {

    private $statement;
    private $repository;
    private $cursors;
    private $processedCursors;

    public function __construct(Array $cursors, Statement $statement, ServiceEntityRepository $repository) {
        $this->statement = $statement;
        $this->repository = $repository;
        $this->cursors = $cursors;
        $this->processedCursors = [];
        $this->bindCursors();
    }

    public function execute() {
        if ($this->statement) {
            $this->statement->execute();
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
        return $this;
    }

    public function fetchAll($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0) {

        if ($this->statement) {
            return $this->statement->fetchAll($fetchMode, $cursorOrientation, $cursorOffset);
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
    }

    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0) {

        if ($this->statement) {
            return $this->statement->fetch($fetchMode, $cursorOrientation, $cursorOffset);
        } else {
            throw new ProcedureStatementEmptySqlException();
        }
    }

    private function bindCursors() {

        if ($this->statement && count($this->cursors) > 0) {

            $processedCursors = [];

            foreach ($this->cursors as $param => $cursor) {
                $this->statement->bindParam($param, $processedCursors[$param], OCI8::PARAM_CURSOR);
            }

            $this->statement->execute();

            foreach ($processedCursors as $param => $cursor) {
                $cursor->execute();
                $this->processedCursors[$param] = $cursor;
            }
        }
    }

    public function fetchEntityCursor(string $name, string $identifier, Array &$result) {

        while ($row = $this->processedCursors[$name]->fetch()) {

            $result[] = $this->repository->find($row[$identifier]);
        }

        $this->processedCursors[$name]->closeCursor();

        return $this;
    }

    public function fetchRawCursor(string $name, Array &$result) {

        $result = $this->processedCursors[$name];
        return $this;
    }

    public function close() {
        $this->statement->closeCursor();
        $this->statement = null;
    }

}
