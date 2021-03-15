<?php
namespace OCI8Repository\Repository;

class OCI8BaseRepositorySupportClassException extends \Exception{
    
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL) {
        return parent::__construct('The supported class must be defined with protected encapsulation in you repository class '.
                PHP_EOL.' Example : protected $support = App\Entity\EntityClass::class', $code, $previous);
    }
}