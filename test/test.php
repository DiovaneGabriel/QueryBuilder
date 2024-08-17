<?php

use DBarbieri\QueryBuilder\Model;

require __DIR__ . '/../vendor/autoload.php';

$model = Model::getModel(Model::SGBD_POSTGRE, 'aml', 'Aml@passw0rd', 'aml', 'postgres', 5432);

$values = $model->getNSequenceValues("SEQ_PCLIEN", 20);

echo '<pre>';
var_dump($values);
die();
