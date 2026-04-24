<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/../data/lembretes.json';
$dataFileAgendas = __DIR__ . '/../data/agendas.json';

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

switch ($metodo) {
    case 'GET':
        $lembretes = ler($dataFile, 'lembretes', []);
        $agendas = ler($dataFileAgendas, 'agendas', []);
        $mapa = [];
        foreach ($agendas as $a) $mapa[(int)$a['id']] = $a['titulo'] ?? '';
        foreach ($lembretes as &$l) $l['agenda_titulo'] = $mapa[(int)($l['agenda_id'] ?? 0)] ?? '-';
        unset($l);
        responder(['lembretes' => $lembretes]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || empty($body['agenda_id']) || empty(trim($body['mensagem'] ?? ''))) {
            responder(['erro' => 'Agenda e mensagem são obrigatórios'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['lembretes' => []];
        $novoId = 1;
        foreach ($dados['lembretes'] as $l) { if (($l['id'] ?? 0) >= $novoId) $novoId = $l['id'] + 1; }
        $dados['lembretes'][] = [
            'id' => $novoId,
            'agenda_id' => (int)$body['agenda_id'],
            'mensagem' => trim($body['mensagem']),
            'data_lembrete' => trim($body['data_lembrete'] ?? date('Y-m-d H:i:s')),
            'lido' => false,
            'criado_em' => date('Y-m-d H:i:s')
        ];
        if (salvar($dataFile, $dados)) responder(['id' => $novoId, 'mensagem' => 'Lembrete criado']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    case 'PUT':
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || empty($body['id'])) {
            responder(['erro' => 'ID obrigatório'], 400);
            exit;
        }
        $id = (int)$body['id'];
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['lembretes' => []];
        foreach ($dados['lembretes'] as $i => $l) {
            if ((int)$l['id'] === $id) {
                if (isset($body['mensagem'])) $dados['lembretes'][$i]['mensagem'] = trim($body['mensagem']);
                if (isset($body['data_lembrete'])) $dados['lembretes'][$i]['data_lembrete'] = trim($body['data_lembrete']);
                if (isset($body['lido'])) $dados['lembretes'][$i]['lido'] = (bool)$body['lido'];
                if (isset($body['agenda_id'])) $dados['lembretes'][$i]['agenda_id'] = (int)$body['agenda_id'];
                if (salvar($dataFile, $dados)) responder(['mensagem' => 'Lembrete atualizado']);
                else responder(['erro' => 'Erro ao salvar'], 500);
                exit;
            }
        }
        responder(['erro' => 'Lembrete não encontrado'], 404);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { responder(['erro' => 'ID inválido'], 400); exit; }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['lembretes' => []];
        $dados['lembretes'] = array_values(array_filter($dados['lembretes'], fn($l) => (int)$l['id'] === $id));
        if (salvar($dataFile, $dados)) responder(['mensagem' => 'Lembrete excluído']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    default:
        responder(['erro' => 'Método não permitido'], 405);
}
