<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs - Sistema de Agendas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --vermelho-easyjur: #E5293F; --vermelho-escuro: #A82130; --cinza-escuro: #191919; --branco-gelo: #F9F9F9; --chumbo: #7F919A; --cinza: #ACBAC2; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: var(--branco-gelo); color: var(--cinza-escuro); min-height: 100vh; display: flex; flex-direction: column; }
        nav { background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); padding: 1rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        nav ul { list-style: none; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; }
        nav a { color: white; text-decoration: none; font-weight: 500; }
        nav a:hover { opacity: 0.9; text-decoration: underline; }
        nav .logo { font-weight: 700; font-size: 1.25rem; }
        main { flex: 1; padding: 2rem; max-width: 1100px; margin: 0 auto; width: 100%; }
        main h1 { font-size: 1.75rem; margin-bottom: 1rem; }
        .toolbar { margin-bottom: 1rem; }
        .filtro { padding: 0.4rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        th { background: var(--cinza); color: white; font-weight: 600; }
        tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-criar { background: #d1fae5; color: #065f46; }
        .badge-editar { background: #dbeafe; color: #1e40af; }
        .badge-excluir { background: #fee2e2; color: #991b1b; }
        .loading, .vazio, .erro { text-align: center; padding: 2rem; color: var(--chumbo); }
        .erro { color: var(--vermelho-easyjur); }
        .dados-json { font-size: 0.75rem; font-family: monospace; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        footer { padding: 1rem 2rem; background: var(--cinza-escuro); color: var(--cinza); font-size: 0.875rem; text-align: center; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="../../index.php" class="logo">EASYJUR</a></li>
            <li><a href="../../index.php">Home</a></li>
            <li><a href="../pessoas/">Pessoas</a></li>
            <li><a href="../agendas/">Agendas</a></li>
            <li><a href="../lembretes/">Lembretes</a></li>
            <li><a href="../tags/">Tags</a></li>
            <li><a href="index.php">Logs</a></li>
        </ul>
    </nav>
    <main>
        <h1>Logs de auditoria</h1>
        <div class="toolbar">
            <select id="filtroEntidade" class="filtro">
                <option value="">Todas entidades</option>
                <option value="pessoas">Pessoas</option>
                <option value="agendas">Agendas</option>
            </select>
        </div>
        <div id="conteudo"><div class="loading">Carregando...</div></div>
    </main>
    <footer>Sistema de Cadastro de Agendas - EasyJur &copy; Projeto de Estudos</footer>

    <script>
        const API = '../../api/logs.php';
        let dadosCompletos = [];

        function escapar(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

        function formatarData(d) {
            if (!d) return '-';
            const dt = new Date(d.replace(' ', 'T'));
            return dt.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        function badgeAcao(a) {
            const m = { criar: 'Criar', editar: 'Editar', excluir: 'Excluir' };
            const c = 'badge-' + (a || '');
            return '<span class="badge ' + c + '">' + escapar(m[a] || a) + '</span>';
        }

        document.getElementById('filtroEntidade').addEventListener('change', aplicarFiltro);

        function aplicarFiltro() {
            const ent = document.getElementById('filtroEntidade').value;
            let f = dadosCompletos;
            if (ent) f = dadosCompletos.filter(l => (l.entidade || '') === ent);
            renderizar(f);
        }

        function renderizar(logs) {
            const c = document.getElementById('conteudo');
            if (!logs.length) { c.innerHTML = '<div class="vazio">Nenhum log registrado.</div>'; return; }
            let html = '<table><thead><tr><th>Data</th><th>Entidade</th><th>ID</th><th>Ação</th><th>Resumo</th></tr></thead><tbody>';
            logs.forEach(l => {
                const resumo = l.acao === 'criar' ? (l.dados_novos && (l.dados_novos.nome || l.dados_novos.titulo)) : '';
                html += '<tr><td>' + formatarData(l.data) + '</td><td>' + escapar(l.entidade) + '</td><td>' + escapar(l.entidade_id) + '</td><td>' + badgeAcao(l.acao) + '</td><td class="dados-json">' + escapar(resumo || '-') + '</td></tr>';
            });
            html += '</tbody></table>';
            c.innerHTML = html;
        }

        async function carregarLista() {
            const c = document.getElementById('conteudo');
            c.innerHTML = '<div class="loading">Carregando...</div>';
            try {
                const res = await fetch(API + '?limite=200');
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro');
                dadosCompletos = data.logs || [];
                aplicarFiltro();
            } catch (e) {
                c.innerHTML = '<div class="erro">' + escapar(e.message) + '</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', carregarLista);
    </script>
</body>
</html>
