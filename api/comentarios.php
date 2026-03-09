<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/../data/comentarios.json';

function ler($arq, $chave, $padrao = []) {
    if (!file_exists($arq)) return $padrao;
    $d = json_decode(file_get_contents($arq), true);
    return $d[$chave] ?? $padrao;
}

function salvar($arq, $dados) {
    return file_put_contents($arq, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}

$metodo = $_SERVER['REQUEST_METHOD'];
$agendaId = isset($_GET['agenda_id']) ? (int)$_GET['agenda_id'] : null;

switch ($metodo) {
    case 'GET':
        $comentarios = ler($dataFile, 'comentarios', []);
        if ($agendaId !== null) {
            $comentarios = array_values(array_filter($comentarios, fn($c) => (int)($c['agenda_id'] ?? 0) === $agendaId));
        }
        usort($comentarios, fn($a, $b) => strcmp($b['criado_em'] ?? '', $a['criado_em'] ?? ''));
        responder(['comentarios' => $comentarios]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || empty($body['agenda_id']) || empty(trim($body['texto'] ?? ''))) {
            responder(['erro' => 'Agenda e texto são obrigatórios'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['comentarios' => []];
        $novoId = 1;
        foreach ($dados['comentarios'] as $c) { if (($c['id'] ?? 0) >= $novoId) $novoId = $c['id'] + 1; }
        $dados['comentarios'][] = [
            'id' => $novoId,
            'agenda_id' => (int)$body['agenda_id'],
            'texto' => trim($body['texto']),
            'criado_em' => date('Y-m-d H:i:s')
        ];
        if (salvar($dataFile, $dados)) responder(['id' => $novoId, 'mensagem' => 'Comentário adicionado']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { responder(['erro' => 'ID inválido'], 400); exit; }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['comentarios' => []];
        $dados['comentarios'] = array_values(array_filter($dados['comentarios'], fn($c) => (int)$c['id'] !== $id));
        if (salvar($dataFile, $dados)) responder(['mensagem' => 'Comentário excluído']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    default:
        responder(['erro' => 'Método não permitido'], 405);
}
