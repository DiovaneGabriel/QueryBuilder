<?php

namespace DBarbieri\QueryBuilder;

use PDO;

class ModelMySQL extends Model
{
    public function __construct($connection = false, $user = false, $password = false, $database = false, $host = 'localhost', $port = 3306)
    {
        if (!$connection) {

            //$dsn = "pgsql:host=$host;port=$port;dbname=$database;user=$user;password=$password";

            /*$connection = new PDO($dsn);
            $connection->setAttribute(PDO::ATTR_AUTOCOMMIT, false);*/

            $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

            $connection = new PDO($dsn, $user, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        parent::__construct($connection);
    }

    /*public function getSequenceNextVal($sequenceName, $returnSequenceValue = true)
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
    }*/
}
