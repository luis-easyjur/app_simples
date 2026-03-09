<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/../data/tipos_agenda.json';

if (!file_exists($dataFile)) {
    echo json_encode(['tipos' => []]);
    exit;
}

$conteudo = file_get_contents($dataFile);
$dados = json_decode($conteudo, true);
echo json_encode($dados ?: ['tipos' => []], JSON_UNESCAPED_UNICODE);
