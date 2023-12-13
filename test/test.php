<?php

require_once "src/QueryBuilder/Model.php";

use QueryBuilder\Model;

$model = Model::getModel(Model::SGBD_SQLSERVER, 'user', 'password', 'database', 'host', 1433);

$row = $model
    ->select("'x'")
    ->getRow();

echo '<pre>';
var_dump($row);
die();
