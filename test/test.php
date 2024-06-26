<?php

use DBarbieri\QueryBuilder\Model;

require __DIR__ . '/../vendor/autoload.php';

$model = Model::getModel(Model::SGBD_MYSQL, 'root', 'P@ssw0rd', 'kto', 'mariadb-kto', 3306);

/*$rows = [
    "cep" => "74414035",
    "codigo" => "MOCK8181706634339957153MF45",
    "complemento" => NULL,
    "dt_operacao" => "2005-06-04",
    "especie" => false,
    "hr_operacao" => "12:09:30",
    "id_operacao_disponivel" => "8005",
    "id_proc_cliente" => 1,
    "id_proc_contrato" => 1,
    "id_proc_operacao_realizada" => $model->getSequenceNextVal("SEQ_POPERE", false),
    "id_produto" => 800,
    "outros" => NULL,
    "patrimonio" => 81700,
    "renda" => 0,
    "tipo_operacao" => 2,
    "valor" => 23.41,
    "valor_esperado" => 23.41,
    "nome_contraparte" => "Diego Felipe D'ávila Sobrinho"
];

$model->insertBatch('proc_operacao_realizada', [$rows]);*/

$row = $model
    ->select("'x'")
    ->getRow();

echo '<pre>';
var_dump($row);
die();
