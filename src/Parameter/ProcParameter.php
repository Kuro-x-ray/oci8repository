<?php 

namespace OCI8Repository\Parameter;

use PDO;

class ProcParameter
{
    /**
     * @var string
     */
    public $name;
 
    /**
     * @var int
     */
    public $bind;
    
    /**
     * 
     * @var string
     */
    public $value;
    
    /**
     * @var int
     */
    public $type;
    
    
    
    const PARAM_IN = 1;
    
    const PARAM_OUT = 2;
    
    const PARAM_CURSOR = 3;
    
    
    function __construct(string $name, int $bind, int $type = PDO::PARAM_STR) {
        $this->name = $name;
        $this->bind = $bind;
        $this->type = $type;
        
    }

    function getType(): int {
        return $this->type;
    }

    function setType(int $type): void {
        $this->type = $type;
    }

        function getName(): string {
        return $this->name;
    }

    function getBind(): int {
        return $this->bind;
    }

    function getValue() {
        return $this->value;
    }

    function setName(string $name): void {
        $this->name = $name;
    }

    function setBind(int $bind): void {
        $this->bind = $bind;
    }

    function setValue($value): void {
        $this->value = $value;
    }


 
}