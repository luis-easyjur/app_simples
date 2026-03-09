<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/logger.php';

$dataFileAgendas = __DIR__ . '/../data/agendas.json';
$dataFilePessoas = __DIR__ . '/../data/pessoas.json';
$dataFileTipos = __DIR__ . '/../data/tipos_agenda.json';
$dataFileTags = __DIR__ . '/../data/tags.json';

function lerArquivo($arquivo, $chave, $padrao = []) {
    if (!file_exists($arquivo)) return $padrao;
    $conteudo = file_get_contents($arquivo);
    $dados = json_decode($conteudo, true);
    return $dados[$chave] ?? $padrao;
}

function salvarAgendas($arquivo, $dados) {
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($arquivo, $json) !== false;
}

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}

function verificarConflito($agendas, $data, $horaInicio, $horaFim, $excluirId = null) {
    foreach ($agendas as $a) {
        if ($excluirId && (int)$a['id'] === (int)$excluirId) continue;
        if ($a['data_agenda'] !== $data) continue;
        $aIni = strtotime($a['hora_inicio'] ?? '00:00');
        $aFim = strtotime($a['hora_fim'] ?? '23:59');
        $bIni = strtotime($horaInicio);
        $bFim = strtotime($horaFim);
        if ($bIni < $aFim && $bFim > $aIni) return true;
    }
    return false;
}

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $agendas = lerArquivo($dataFileAgendas, 'agendas', []);
        $pessoas = lerArquivo($dataFilePessoas, 'pessoas', []);
        $tipos = lerArquivo($dataFileTipos, 'tipos', []);
        $tags = lerArquivo($dataFileTags, 'tags', []);
        $mapaPessoas = [];
        foreach ($pessoas as $p) $mapaPessoas[(int)$p['id']] = $p['nome'] ?? '';
        $mapaTipos = [];
        foreach ($tipos as $t) $mapaTipos[(int)$t['id']] = $t['nome'] ?? '';
        $mapaTags = [];
        foreach ($tags as $t) $mapaTags[(int)$t['id']] = ['nome' => $t['nome'] ?? '', 'cor' => $t['cor'] ?? '#7F919A'];
        foreach ($agendas as &$a) {
            $a['pessoa_nome'] = $mapaPessoas[(int)($a['pessoa_id'] ?? 0)] ?? '-';
            $a['tipo_nome'] = $mapaTipos[(int)($a['tipo_id'] ?? 0)] ?? '-';
            $tagIds = $a['tag_ids'] ?? [];
            $a['tags'] = array_map(fn($id) => $mapaTags[(int)$id] ?? null, is_array($tagIds) ? $tagIds : []);
            $a['tags'] = array_values(array_filter($a['tags']));
        }
        unset($a);
        responder(['agendas' => $agendas]);
        break;

    case 'POST':
        $body = file_get_contents('php://input');
        $input = json_decode($body, true);
        if (!$input || empty(trim($input['titulo'] ?? '')) || empty($input['pessoa_id'] ?? '')) {
            responder(['erro' => 'Título e pessoa são obrigatórios'], 400);
            exit;
        }
        $dataAgenda = trim($input['data_agenda'] ?? date('Y-m-d'));
        $horaInicio = trim($input['hora_inicio'] ?? '09:00');
        $horaFim = trim($input['hora_fim'] ?? '10:00');
        if (strtotime($dataAgenda) > strtotime(date('Y-m-d'))) {
            responder(['erro' => 'Data não pode ser no passado'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFileAgendas), true) ?: ['agendas' => []];
        if (verificarConflito($dados['agendas'], $dataAgenda, $horaInicio, $horaFim)) {
            responder(['erro' => 'Já existe agenda neste horário'], 400);
            exit;
        }
        $novoId = 1;
        foreach ($dados['agendas'] as $a) {
            if (($a['id'] ?? 0) >= $novoId) $novoId = $a['id'] + 1;
        }
        $agora = date('Y-m-d H:i:s');
        $tagIds = $input['tag_ids'] ?? [];
        if (!is_array($tagIds)) $tagIds = [];
        $novaAgenda = [
            'id' => $novoId,
            'pessoa_id' => (int)$input['pessoa_id'],
            'tipo_id' => (int)($input['tipo_id'] ?? 0),
            'tag_ids' => array_map('intval', $tagIds),
            'titulo' => trim($input['titulo'] ?? ''),
            'data_agenda' => $dataAgenda,
            'hora_inicio' => $horaInicio,
            'hora_fim' => $horaFim,
            'descricao' => trim($input['descricao'] ?? ''),
            'status' => $input['status'] ?? 'agendado',
            'criado_em' => $agora,
            'updated_at' => $agora
        ];
        $dados['agendas'][] = $novaAgenda;
        if (salvarAgendas($dataFileAgendas, $dados)) {
            registrarLog('agendas', $novoId, 'criar', null, $novaAgenda);
            responder(['id' => $novoId, 'mensagem' => 'Agenda cadastrada com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    case 'PUT':
        $body = file_get_contents('php://input');
        $input = json_decode($body, true);
        if (!$input || empty($input['id']) || empty(trim($input['titulo'] ?? ''))) {
            responder(['erro' => 'ID e título são obrigatórios'], 400);
            exit;
        }
        $id = (int)$input['id'];
        $dataAgenda = trim($input['data_agenda'] ?? '');
        $horaInicio = trim($input['hora_inicio'] ?? '09:00');
        $horaFim = trim($input['hora_fim'] ?? '10:00');
        if ($dataAgenda && strtotime($dataAgenda) < strtotime(date('Y-m-d'))) {
            responder(['erro' => 'Data não pode ser no passado'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFileAgendas), true) ?: ['agendas' => []];
        if ($dataAgenda && verificarConflito($dados['agendas'], $dataAgenda, $horaInicio, $horaFim, $id)) {
            responder(['erro' => 'Já existe agenda neste horário'], 400);
            exit;
        }
        $encontrado = false;
        foreach ($dados['agendas'] as $i => $a) {
            if ((int)$a['id'] === $id) {
                $antigo = $a;
                $tagIds = $input['tag_ids'] ?? $a['tag_ids'] ?? [];
                if (!is_array($tagIds)) $tagIds = [];
                $dados['agendas'][$i] = [
                    'id' => $id,
                    'pessoa_id' => (int)($input['pessoa_id'] ?? $a['pessoa_id']),
                    'tipo_id' => (int)($input['tipo_id'] ?? $a['tipo_id'] ?? 0),
                    'tag_ids' => array_map('intval', $tagIds),
                    'titulo' => trim($input['titulo'] ?? ''),
                    'data_agenda' => $dataAgenda ?: ($a['data_agenda'] ?? date('Y-m-d')),
                    'hora_inicio' => $horaInicio ?: ($a['hora_inicio'] ?? '09:00'),
                    'hora_fim' => $horaFim ?: ($a['hora_fim'] ?? '10:00'),
                    'descricao' => trim($input['descricao'] ?? $a['descricao'] ?? ''),
                    'status' => $input['status'] ?? $a['status'] ?? 'agendado',
                    'criado_em' => $a['criado_em'] ?? date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            responder(['erro' => 'Agenda não encontrada'], 404);
            exit;
        }
        if (salvarAgendas($dataFileAgendas, $dados)) {
            registrarLog('agendas', $id, 'editar', $antigo ?? null, $dados['agendas'][$i]);
            responder(['mensagem' => 'Agenda atualizada com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            responder(['erro' => 'ID inválido'], 400);
            exit;
        }
        $dados = json_decode(file_get_contents($dataFileAgendas), true) ?: ['agendas' => []];
        $novasAgendas = [];
        $excluido = null;
        foreach ($dados['agendas'] as $a) {
            if ((int)$a['id'] !== $id) $novasAgendas[] = $a;
            else $excluido = $a;
        }
        if (!$excluido) {
            responder(['erro' => 'Agenda não encontrada'], 404);
            exit;
        }
        $dados['agendas'] = $novasAgendas;
        if (salvarAgendas($dataFileAgendas, $dados)) {
            registrarLog('agendas', $id, 'excluir', $excluido, null);
            responder(['mensagem' => 'Agenda excluída com sucesso']);
        } else {
            responder(['erro' => 'Erro ao salvar'], 500);
        }
        break;

    default:
        responder(['erro' => 'Método não permitido'], 405);
}
