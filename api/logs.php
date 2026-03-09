<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/../data/logs.json';

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(['erro' => 'Método não permitido'], 405);
    exit;
}

$logs = [];
if (file_exists($dataFile)) {
    $d = json_decode(file_get_contents($dataFile), true);
    $logs = $d['logs'] ?? [];
} 
usort($logs, fn($a, $b) => strcmp($b['data'] ?? '', $a['data'] ?? ''));

$entidade = $_GET['entidade'] ?? null;
$entidadeId = isset($_GET['entidade_id']) ? (int)$_GET['entidade_id'] : null;
if ($entidade) $logs = array_values(array_filter($logs, fn($l) => ($l['entidade'] ?? '') === $entidade));
if ($entidadeId !== null) $logs = array_values(array_filter($logs, fn($l) => (int)($l['entidade_id'] ?? 0) === $entidadeId));

$limite = isset($_GET['limite']) ? min(500, max(1, (int)$_GET['limite'])) : 100;
$logs = array_slice($logs, 0, $limite);

responder(['logs' => $logs]);
