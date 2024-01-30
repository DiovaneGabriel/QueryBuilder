<?php

namespace DBarbieri\QueryBuilder\Values;

class Literal
{

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }    

    /**
     * Get the value of value
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
