<?php

namespace OCI8Repository\Repository;


use EcPhp\DoctrineOci8\Doctrine\DBAL\Driver\OCI8\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\DBAL\DriverManager;
use OCI8Repository\Statement\OCI8ProcedureStatementPrepare;

/**
 * @author Gino Mazzola <Gino.MAZZOLA@ext.ec.europa.eu>
 * 
 * Abstract class that allows to execute procedures by calling them by their name and their parameters.
 * With compatibility of OCI8 cursors
 * 
 * Example: 
 * 
 * $this->doProcedure('PACKAGE.procedure(:parameter_1, :parameter_2)')
 *      ->bindOut('parameter_1', $result)
 *      ->bindCursor('parameter_2', $cursor)
 *      ->execute()
 *      ->fetchCursor($cursor, 'table_identifier_name', $cursorResult)
 *      ->close();
 */
abstract class OCI8BaseRepository extends ServiceEntityRepository {

    private $params;

    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry, ParameterBagInterface $params) {

        if (!property_exists($this, 'support')) {
            throw new BaseRepositorySupportClassException();
        } else {
            $rp = new \ReflectionProperty($this, 'support');

            if (!$rp->isProtected()) {
                throw new OCI8BaseRepositorySupportClassException();
            }
        }

        parent::__construct($registry, $this->support);
        $this->params = $params;
    }
    /**
     * @return Doctrine\DBAL\Connection
     */
    private function getConnection() {

        $params = [
            'dbname' => $this->params->get('database_name'),
            'user' => $this->params->get('database_user'),
            'password' => $this->params->get('database_password'),
            'host' => $this->params->get('database_host'),
            'port' => $this->params->get('database_port'),
            'persistent' => true,
            'driverClass' => Driver::class
        ];

        return DriverManager::getConnection($params, $this->getEntityManager()->getConfiguration());
    }
    
    /**
     * @return string
     */
    protected function getOutValue() {
        return uniqid();
    }
    /**
     * @param string $query
     * @return ProcedureStatement
     */
    protected function buildProcedureStatement(string $query) : OCI8ProcedureStatementPrepare {
        return new OCI8ProcedureStatementPrepare($this, $this->getConnection(), $query);
    }

}
