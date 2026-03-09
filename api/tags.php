<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/../data/tags.json';

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
        responder(['tags' => ler($dataFile, 'tags', [])]);
        break;

    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || empty(trim($body['nome'] ?? ''))) {
            responder(['erro' => 'Nome é obrigatório'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['tags' => []];
        $novoId = 1;
        foreach ($dados['tags'] as $t) { if (($t['id'] ?? 0) >= $novoId) $novoId = $t['id'] + 1; }
        $dados['tags'][] = [
            'id' => $novoId,
            'nome' => trim($body['nome']),
            'cor' => trim($body['cor'] ?? '#7F919A')
        ];
        if (salvar($dataFile, $dados)) responder(['id' => $novoId, 'mensagem' => 'Tag criada']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    case 'PUT':
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || empty($body['id'])) {
            responder(['erro' => 'ID obrigatório'], 400);
            exit;
        }
        $id = (int)$body['id'];
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['tags' => []];
        foreach ($dados['tags'] as $i => $t) {
            if ((int)$t['id'] === $id) {
                $dados['tags'][$i]['nome'] = trim($body['nome'] ?? $t['nome']);
                $dados['tags'][$i]['cor'] = trim($body['cor'] ?? $t['cor'] ?? '#7F919A');
                if (salvar($dataFile, $dados)) responder(['mensagem' => 'Tag atualizada']);
                else responder(['erro' => 'Erro ao salvar'], 500);
                exit;
            }
        }
        responder(['erro' => 'Tag não encontrada'], 404);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { responder(['erro' => 'ID inválido'], 400); exit; }
        $dados = json_decode(file_get_contents($dataFile), true) ?: ['tags' => []];
        $dados['tags'] = array_values(array_filter($dados['tags'], fn($t) => (int)$t['id'] !== $id));
        if (salvar($dataFile, $dados)) responder(['mensagem' => 'Tag excluída']);
        else responder(['erro' => 'Erro ao salvar'], 500);
        break;

    default:
        responder(['erro' => 'Método não permitido'], 405);
}
