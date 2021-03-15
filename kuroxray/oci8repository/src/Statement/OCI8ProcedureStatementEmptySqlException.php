<?php

namespace OCI8Repository\Statement;

class OCI8ProcedureStatementEmptySqlException extends \Exception {

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL) {
        return parent::__construct('Empty prepared statement ... First add a SQL Query with the method "buildProcedureStatement($query)"', $code, $previous);
    }

}
