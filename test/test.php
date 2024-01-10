<?php

use DBarbieri\QueryBuilder\Model;

require __DIR__ . '/../vendor/autoload.php';

$model = Model::getModel(Model::SGBD_SQLSERVER, 'user', 'password', 'database', 'host', 1433);

$row = $model
    ->select("'x'")
    ->getRow();

echo '<pre>';
var_dump($row);
die();
