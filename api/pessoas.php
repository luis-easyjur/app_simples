<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/logger.php';

$dataFile = __DIR__ . '/../data/pessoas.json';

function lerPessoas($arquivo) {
    if (!file_exists($arquivo)) {
        return ['pessoas' => []];
    }
    $conteudo = file_get_contents($arquivo);
    $dados = json_decode($conteudo, true);
    return $dados ?: ['pessoas' => []];
}

function salvarPessoas($arquivo, $dados) {
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($arquivo, $json) !== false;
}

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $dados = lerPessoas($dataFile);
        responder($dados);
        break;

    case 'POST':
        $body = file_get_contents('php://input');
        $input = json_decode($body, true);
        if (!$input || empty(trim($input['nome'] ?? ''))) {
            responder(['erro' => 'Nome é obrigatório'], 400);
            exit;
        }
        $email = trim($input['email'] ?? '');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            responder(['erro' => 'E-mail inválido'], 400);
            exit;
        }
        $dados = lerPessoas($dataFile);
        $novoId = 1;
        foreach ($dados['pessoas'] as $p) {
            if (($p['id'] ?? 0) >= $novoId) {
                $novoId = $p['id'] + 1;
            }
        }
        $agora = date('Y-m-d H:i:s');
        $novaPessoa = [
            'id' => $novoId,
            'nome' => trim($input['nome'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'telefone' => trim($input['telefone'] ?? ''),
            'criado_em' => $agora,
            'updated_at' => $agora
        ];
        $dados['pessoas'][] = $novaPessoa;
        if (salvarPessoas($dataFile, $dados)) {
            registrarLog('pessoas', $novoId, 'criar', null, $novaPessoa);
            responder(['id' => $novoId, 'mensagem' => 'Pessoa cadastrada com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    case 'PUT':
        $body = file_get_contents('php://input');
        $input = json_decode($body, true);
        if (!$input || empty($input['id']) || empty(trim($input['nome'] ?? ''))) {
            responder(['erro' => 'ID e nome são obrigatórios'], 400);
            exit;
        }
        $email = trim($input['email'] ?? '');
        if ($email && !filter_var($email, FILTER_SANITIZE_EMAIL)) {
            responder(['erro' => 'E-mail inválido'], 400);
            exit;
        }
        $id = (int) $input['id'];
        $dados = lerPessoas($dataFile);
        $encontrado = false;
        foreach ($dados['pessoas'] as $i => $p) {
            if ((int) $p['id'] === $id) {
                $antigo = $p;
                $dados['pessoas'][$i] = [
                    'id' => $id,
                    'nome' => trim($input['nome'] ?? ''),
                    'email' => trim($input['email'] ?? ''),
                    'telefone' => trim($input['telefone'] ?? ''),
                    'criado_em' => $p['criado_em'] ?? date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            responder(['erro' => 'Pessoa não encontrada'], 404);
            exit;
        }
        if (salvarPessoas($dataFile, $dados)) {
            registrarLog('pessoas', $id, 'editar', $antigo ?? null, $dados['pessoas'][$i]);
            responder(['mensagem' => 'Pessoa atualizada com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            responder(['erro' => 'ID inválido'], 400);
            exit;
        }
        $dados = lerPessoas($dataFile);
        $novasPessoas = [];
        $excluido = null;
        foreach ($dados['pessoas'] as $p) {
            if ((int) $p['id'] !== $id) {
                $novasPessoas[] = $p;
            } else {
                $excluido = $p;
            }
        }
        if (!$excluido) {
            responder(['erro' => 'Pessoa não encontrada'], 404);
            exit;
        }
        $dados['pessoas'] = $novasPessoas;
        if (salvarPessoas($dataFile, $dados)) {
            registrarLog('pessoas', $id, 'excluir', $excluido, null);
            responder(['mensagem' => 'Pessoa excluída com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    default:
        responder(['erro' => 'Método não permitido'], 405);
}
