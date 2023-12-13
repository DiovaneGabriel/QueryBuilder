<?php

namespace QueryBuilder;

require_once 'ModelSQLServer.php';
require_once 'ModelPostgreSQL.php';

use PDO;

class Model
{

    public const SGBD_POSTGRE = 'postgre';
    public const SGBD_SQLSERVER = 'sqlserver';

    protected const ARRAY = 'array';
    protected const OBJECT = 'object';

    protected $connection;

    protected $isDistinct;
    protected $binds;
    protected $cache;
    protected $columns;
    protected $conditions;
    protected $statement;
    protected $table;
    protected $limitRows;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public static function getModel($sgbd, $user, $password, $database, $host, $port)
    {
        if ($sgbd == self::SGBD_SQLSERVER) {
            $model = new ModelSQLServer(false, $user, $password, $database, $host, $port);
        } elseif ($sgbd == self::SGBD_POSTGRE) {
            $model = new ModelPostgreSQL(false, $user, $password, $database, $host, $port);
        }

        return $model;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function distinct()
    {
        $this->isDistinct = true;
        return $this;
    }

    public function select($columns)
    {
        if (!is_array($columns)) {
            $this->select([$columns]);
        } elseif (count($columns) > 0) {
            if (!is_array($this->columns)) {
                $this->columns = [];
            }
            $this->columns = array_merge($this->columns, $columns);
        }

        return $this;
    }

    public function from($table)
    {
        if (is_array($table)) {
            $this->table = $this->table ? $this->table : [];
            $this->table = array_merge($this->table, $table);
        } else {
            $this->from([$table]);
        }

        return $this;
    }

    public function where($field, $value = false)
    {
        if ($value !== false) {
            $condition = $field . ' = ' . $this->createBind($field, $value);
        } else {
            $condition = $field;
        }

        $this->conditions[] = $condition;

        return $this;
    }

    public function limit($limit)
    {
        $this->limitRows = $limit;

        return $this;
    }

    public function insert($table, $data)
    {

        $this->columns = array_keys($data);
        $this->createBinds($data);

        $this->table = $table;

        $sql = "INSERT INTO " . $this->table . " (" . implode(',', $this->columns) . ") VALUES (" . implode(",", array_keys($this->binds)) . ");";

        $this->executeSql($sql);

        $this->flush();

        return true;
    }

    public function getSequenceNextVal($sequenceName)
    {
        return mt_rand(1, 1000000000);
    }

    public function delete($table)
    {
        $this->table = $table;
        $sql = 'DELETE FROM ' . $this->table;

        $sql .= $this->getCompiledConditions();

        $this->executeSql($sql);

        $this->flush();

        return true;
    }

    public function withCache(int $time = 60)
    {
        $this->cache = $time;
        return $this;
    }

    public function getResult()
    {
        return $this->get(self::ARRAY);
    }

    public function getRow()
    {
        return $this->get(self::OBJECT);
    }

    protected function bindValues()
    {
        if ($this->binds) {
            foreach ($this->binds as $key => $value) {

                if ($value === true) {
                    $value = 1;
                } elseif ($value === false) {
                    $value = 0;
                }

                $this->statement->bindValue($key, $value);
            }
        }
    }

    protected function createBind($key, $value)
    {
        $key = ':v' . md5($key);
        $this->binds[$key] = $value;

        return $key;
    }

    protected function createBinds(array $data)
    {
        $binds = false;
        foreach ($data as $key => $value) {
            $binds[$this->createBind($key, $value)] = $value;
        }

        return $binds;
    }

    protected function get(string $mode)
    {

        $sql = $this->getCompiledSelect();
        $cacheKey = md5($mode . $sql);

        $cache = $this->cache ? $this->getCache($cacheKey) : false;

        if (!$cache) {
            $this->executeSql($sql);

            if ($mode == self::OBJECT) {
                $result = $this->statement->fetch(PDO::FETCH_OBJ);
            } else {
                $result = $this->statement->fetchAll(PDO::FETCH_ASSOC);
            }

            if ($this->cache) {
                $this->setCache($cacheKey, $result, $this->cache);
            }
        } else {
            $result = $cache;
        }

        $this->flush();

        return $result;
    }

    protected function executeSql($sql)
    {
        try {
            $this->statement = $this->connection->prepare($sql);
            $this->bindValues();
            $this->statement->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro: " . $e->getMessage() . PHP_EOL . "SQL: " . $sql . " Binds: " . json_encode($this->binds));
        }
    }

    protected function getCompiledSelect()
    {
        $sql = 'SELECT ';

        if ($this->isDistinct) {
            $sql .= 'DISTINCT ';
        }

        if ($this instanceof ModelSQLServer && $this->limitRows) {
            $sql .= "TOP " . $this->limitRows . " ";
        }

        $sql .= implode(',', $this->columns);

        if ($this->table) {
            $sql .= ' FROM ' . implode(',', $this->table);
        }

        $sql .= $this->getCompiledConditions();

        if ($this instanceof ModelPostgreSQL && $this->limitRows) {
            $sql .= " LIMIT " . $this->limitRows . " ";
        }

        return $sql;
    }

    protected function getCompiledConditions()
    {
        return $this->conditions ? ' WHERE ' . implode(' AND ', $this->conditions) : null;
    }

    protected function setCache($key, $data, $time = 60)
    {
        $cache = [
            'expireWhen' => strtotime(date('Y-m-d H:i:s') . ' +' . $time . ' minutes'),
            'data' => $data
        ];

        $_SESSION['cache'][$key] = (object) $cache;
    }

    protected function getCache($key)
    {
        if (isset($_SESSION['cache']) && isset($_SESSION['cache'][$key])) {
            $cache = $_SESSION['cache'][$key];
            if ($cache->expireWhen >= strtotime(date("Y-m-d H:i:s"))) {
                return $cache->data;
            } else {
                unset($_SESSION['cache'][$key]);
            }
        }

        return false;
    }

    protected function flush()
    {
        $this->isDistinct = null;
        $this->binds = null;
        $this->cache = null;
        $this->columns = null;
        $this->conditions = null;
        $this->statement = null;
        $this->table = null;
        $this->limitRows = null;
    }
}
