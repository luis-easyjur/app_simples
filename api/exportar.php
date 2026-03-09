<?php
$tipo = $_GET['tipo'] ?? 'pessoas'; // pessoas ou agendas

$dataFilePessoas = __DIR__ . '/../data/pessoas.json';
$dataFileAgendas = __DIR__ . '/../data/agendas.json';
$dataFileTipos = __DIR__ . '/../data/tipos_agenda.json';

function lerJson($arquivo, $chave) {
    if (!file_exists($arquivo)) return [];
    $d = json_decode(file_get_contents($arquivo), true);
    return $d[$chave] ?? [];
}

if ($tipo === 'pessoas') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pessoas.csv"');
    $pessoas = lerJson($dataFilePessoas, 'pessoas');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    fputcsv($out, ['ID', 'Nome', 'E-mail', 'Telefone', 'Criado em', 'Atualizado em'], ';');
    foreach ($pessoas as $p) {
        fputcsv($out, [
            $p['id'] ?? '',
            $p['nome'] ?? '',
            $p['email'] ?? '',
            $p['telefone'] ?? '',
            $p['criado_em'] ?? '',
            $p['updated_at'] ?? ''
        ], ';');
    }
    fclose($out);
    exit;
}

if ($tipo === 'agendas') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="agendas.csv"');
    $agendas = lerJson($dataFileAgendas, 'agendas');
    $pessoas = lerJson($dataFilePessoas, 'pessoas');
    $tipos = lerJson($dataFileTipos, 'tipos');
    $mapaP = [];
    foreach ($pessoas as $p) $mapaP[(int)$p['id']] = $p['nome'] ?? '';
    $mapaT = [];
    foreach ($tipos as $t) $mapaT[(int)$t['id']] = $t['nome'] ?? '';
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($out, ['ID', 'Pessoa', 'Tipo', 'Título', 'Data', 'Hora Início', 'Hora Fim', 'Status', 'Descrição'], ';');
    foreach ($agendas as $a) {
        fputcsv($out, [
            $a['id'] ?? '',
            $mapaP[(int)($a['pessoa_id'] ?? 0)] ?? '',
            $mapaT[(int)($a['tipo_id'] ?? 0)] ?? '',
            $a['titulo'] ?? '',
            $a['data_agenda'] ?? '',
            $a['hora_inicio'] ?? '',
            $a['hora_fim'] ?? '',
            $a['status'] ?? '',
            $a['descricao'] ?? ''
        ], ';');
    }
    fclose($out);
    exit;
}

http_response_code(400);
echo json_encode(['erro' => 'Tipo inválido. Use: ?tipo=pessoas ou ?tipo=agendas']);
