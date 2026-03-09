<?php
function registrarLog($entidade, $entidadeId, $acao, $dadosAntes = null, $dadosNovos = null) {
    $arquivo = __DIR__ . '/../data/logs.json';
    $dados = ['logs' => []];
    if (file_exists($arquivo)) {
        $dados = json_decode(file_get_contents($arquivo), true) ?: ['logs' => []];
    }
    $novoId = 1;
    foreach ($dados['logs'] as $l) {
        if (($l['id'] ?? 0) >= $novoId) $novoId = $l['id'] + 1;
    }
    $dados['logs'][] = [
        'id' => $novoId,
        'entidade' => $entidade,
        'entidade_id' => $entidadeId,
        'acao' => $acao,
        'dados_anteriores' => $dadosAntes,
        'dados_novos' => $dadosNovos,
        'data' => date('Y-m-d H:i:s')
    ];
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($arquivo, $json);
}
