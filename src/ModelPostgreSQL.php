<?php

namespace DBarbieri\QueryBuilder;

use DBarbieri\QueryBuilder\Values\Literal;
use PDO;

class ModelPostgreSQL extends Model
{
    public function __construct($connection = false, $user = false, $password = false, $database = false, $host = 'localhost', $port = 5432)
    {
        if (!$connection) {

            $dsn = "pgsql:host=$host;port=$port;dbname=$database;user=$user;password=$password";

            $connection = new PDO($dsn);
            $connection->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        }

        parent::__construct($connection);
    }

    public function getSequenceNextVal($sequenceName, $returnSequenceValue = true)
    {
        $model = new self($this->connection);

        $model->select("nextval('" . $sequenceName . "')");

        if ($returnSequenceValue) {
            $result = $model->getRow();
            return $result->nextval;
        } else {
            return new Literal("(" . $model->getCompiledSelect() . ")");
        }
    }

    public function whereLength($field, $value = false, $operator = '=')
    {
        $sql = "LENGTH(" . $field . ") " . $operator . " '" . $value . "'";
        return $this->where($sql);
    }
}
