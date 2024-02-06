<?php

namespace DBarbieri\QueryBuilder;

use DBarbieri\QueryBuilder\Values\Literal;
use PDO;

class ModelSQLServer extends Model
{

    public function __construct($connection = false, $user = false, $password = false, $database = 'master', $host = 'localhost', $port = 1433)
    {
        if (!$connection) {
            $connection = new PDO("sqlsrv:Server=$host,$port;Database={$database}", $user, $password);
        }

        parent::__construct($connection);
    }

    public function getSequenceNextVal($sequenceName, $returnSequenceValue = true)
    {
        $model = new self($this->connection);

        $model->select("next value for " . $sequenceName . " as nextval");

        if ($returnSequenceValue) {
            $result = $model->getRow();
            return $result->nextval;
        } else {
            return new Literal("(next value for " . $sequenceName.")");
        }
    }

    public function whereLength($field, $value = false, $operator = '=')
    {
        $sql = "LEN(" . $field . ") " . $operator . " '" . $value . "'";
        return $this->where($sql);
    }
}
