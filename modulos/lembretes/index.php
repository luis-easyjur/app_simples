<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembretes - Sistema de Agendas</title>
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
        main { flex: 1; padding: 2rem; max-width: 950px; margin: 0 auto; width: 100%; }
        main h1 { font-size: 1.75rem; margin-bottom: 1rem; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; font-family: inherit; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
        .btn:hover { transform: translateY(-1px); opacity: 0.95; }
        .btn-primario { background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); color: white; }
        .btn-editar { background: var(--chumbo); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-excluir { background: var(--vermelho-escuro); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-cancelar { background: var(--cinza); color: var(--cinza-escuro); }
        .btn-lido { background: #22c55e; color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--cinza); color: white; font-weight: 600; }
        tr:hover { background: #fafafa; }
        tr.lido { opacity: 0.7; }
        .acoes { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .loading, .vazio, .erro { text-align: center; padding: 2rem; color: var(--chumbo); }
        .erro { color: var(--vermelho-easyjur); }
        .toast { position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.5rem; border-radius: 8px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s ease; }
        .toast.sucesso { background: #22c55e; color: white; }
        .toast.erro { background: var(--vermelho-easyjur); color: white; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.ativo { display: flex; }
        .modal { background: white; border-radius: 8px; padding: 2rem; width: 100%; max-width: 420px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .modal h2 { margin-bottom: 1.5rem; font-size: 1.25rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.35rem; font-weight: 500; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; font-size: 0.95rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--vermelho-easyjur); }
        .form-group textarea { min-height: 60px; resize: vertical; }
        .modal-botoes { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .modal-botoes .btn { flex: 1; }
        .modal-confirm-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1100; align-items: center; justify-content: center; }
        .modal-confirm-overlay.ativo { display: flex; }
        .modal-confirm { background: white; border-radius: 8px; padding: 1.5rem; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .modal-confirm p { margin-bottom: 1rem; }
        .modal-confirm .botoes { display: flex; gap: 0.5rem; justify-content: flex-end; }
        .filtro { padding: 0.4rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; margin-right: 0.5rem; }
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
            <li><a href="index.php">Lembretes</a></li>
            <li><a href="../tags/">Tags</a></li>
            <li><a href="../logs/">Logs</a></li>
        </ul>
    </nav>
    <main>
        <h1>Lembretes</h1>
        <div class="toolbar">
            <div>
                <select id="filtroLido" class="filtro">
                    <option value="">Todos</option>
                    <option value="0">Não lidos</option>
                    <option value="1">Lidos</option>
                </select>
            </div>
            <button class="btn btn-primario" onclick="abrirModal()">Novo lembrete</button>
        </div>
        <div id="conteudo"><div class="loading">Carregando...</div></div>
    </main>
    <div class="modal-overlay" id="modalOverlay" onclick="fecharModalSeOverlay(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <h2 id="modalTitulo">Novo lembrete</h2>
            <form id="formLembrete" onsubmit="salvar(event)">
                <input type="hidden" id="lembreteId" value="">
                <div class="form-group">
                    <label for="agenda_id">Agenda *</label>
                    <select id="agenda_id" required><option value="">Selecione...</option></select>
                </div>
                <div class="form-group">
                    <label for="mensagem">Mensagem *</label>
                    <textarea id="mensagem" required placeholder="Texto do lembrete"></textarea>
                </div>
                <div class="form-group">
                    <label for="data_lembrete">Data e hora do lembrete</label>
                    <input type="datetime-local" id="data_lembrete">
                </div>
                <div class="modal-botoes">
                    <button type="button" class="btn btn-cancelar" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primario">Salvar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-confirm-overlay" id="modalConfirm" onclick="fecharConfirmSeOverlay(event)">
        <div class="modal-confirm" onclick="event.stopPropagation()">
            <p id="confirmMsg">Excluir?</p>
            <div class="botoes">
                <button class="btn btn-cancelar" onclick="fecharConfirm()">Cancelar</button>
                <button class="btn btn-excluir" id="confirmBtn">Excluir</button>
            </div>
        </div>
    </div>
    <footer>Sistema de Cadastro de Agendas - EasyJur &copy; Projeto de Estudos</footer>

    <script>
        const API = '../../api/lembretes.php';
        const API_AGENDAS = '../../api/agendas.php';
        let dadosCompletos = [];

        function mostrarToast(m, t = 'sucesso') {
            document.querySelectorAll('.toast').forEach(e => e.remove());
            const toast = document.createElement('div');
            toast.className = 'toast ' + t;
            toast.textContent = m;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function escapar(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

        function formatarDataHora(d) {
            if (!d) return '-';
            const dt = new Date(d.replace(' ', 'T'));
            return dt.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        document.getElementById('filtroLido').addEventListener('change', aplicarFiltro);

        function aplicarFiltro() {
            const v = document.getElementById('filtroLido').value;
            let f = dadosCompletos;
            if (v === '0') f = dadosCompletos.filter(l => !l.lido);
            else if (v === '1') f = dadosCompletos.filter(l => l.lido);
            renderizar(f);
        }

        function renderizar(lembretes) {
            const c = document.getElementById('conteudo');
            if (!lembretes.length) { c.innerHTML = '<div class="vazio">Nenhum lembrete.</div>'; return; }
            let html = '<table><thead><tr><th>Agenda</th><th>Mensagem</th><th>Data lembrete</th><th>Lido</th><th>Ações</th></tr></thead><tbody>';
            lembretes.forEach(l => {
                const cls = l.lido ? ' class="lido"' : '';
                html += '<tr' + cls + '><td>' + escapar(l.agenda_titulo) + '</td><td>' + escapar(l.mensagem) + '</td><td>' + formatarDataHora(l.data_lembrete) + '</td><td>' + (l.lido ? 'Sim' : 'Não') + '</td><td class="acoes">' + (!l.lido ? '<button class="btn btn-lido" onclick="marcarLido(' + l.id + ')">Marcar lido</button>' : '') + '<button class="btn btn-editar" onclick="editar(' + l.id + ')">Editar</button><button class="btn btn-excluir" onclick="excluir(this)" data-id="' + l.id + '">Excluir</button></td></tr>';
            });
            html += '</tbody></table>';
            c.innerHTML = html;
        }

        async function carregarLista() {
            const c = document.getElementById('conteudo');
            c.innerHTML = '<div class="loading">Carregando...</div>';
            try {
                const res = await fetch(API);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro');
                dadosCompletos = data.lembretes || [];
                aplicarFiltro();
            } catch (e) {
                c.innerHTML = '<div class="erro">' + escapar(e.message) + '</div>';
            }
        }

        async function carregarAgendas() {
            const sel = document.getElementById('agenda_id');
            const val = sel.value;
            sel.innerHTML = '<option value="">Selecione...</option>';
            try {
                const res = await fetch(API_AGENDAS);
                const data = await res.json();
                (data.agendas || []).forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.id;
                    o.textContent = (a.titulo || '') + ' - ' + (a.data_agenda || '');
                    sel.appendChild(o);
                });
                sel.value = val || '';
            } catch (e) {}
        }

        async function abrirModal(id = null) {
            document.getElementById('modalTitulo').textContent = id ? 'Editar lembrete' : 'Novo lembrete';
            document.getElementById('lembreteId').value = id || '';
            document.getElementById('mensagem').value = '';
            document.getElementById('data_lembrete').value = '';
            await carregarAgendas();
            if (id) {
                const l = dadosCompletos.find(x => x.id == id);
                if (l) {
                    document.getElementById('agenda_id').value = l.agenda_id || '';
                    document.getElementById('mensagem').value = l.mensagem || '';
                    const dt = (l.data_lembrete || '').replace(' ', 'T').substring(0, 16);
                    document.getElementById('data_lembrete').value = dt || '';
                }
            } else {
                const amanha = new Date();
                amanha.setDate(amanha.getDate() + 1);
                amanha.setHours(8, 0, 0, 0);
                document.getElementById('data_lembrete').value = amanha.toISOString().slice(0, 16);
            }
            document.getElementById('modalOverlay').classList.add('ativo');
        }

        function fecharModal() { document.getElementById('modalOverlay').classList.remove('ativo'); }
        function fecharModalSeOverlay(e) { if (e.target.id === 'modalOverlay') fecharModal(); }

        function abrirConfirm(id, cb) {
            document.getElementById('confirmMsg').textContent = 'Excluir este lembrete?';
            document.getElementById('confirmBtn').onclick = () => { document.getElementById('modalConfirm').classList.remove('ativo'); cb(id); };
            document.getElementById('modalConfirm').classList.add('ativo');
        }
        function fecharConfirm() { document.getElementById('modalConfirm').classList.remove('ativo'); }
        function fecharConfirmSeOverlay(e) { if (e.target.id === 'modalConfirm') fecharConfirm(); }

        async function salvar(e) {
            e.preventDefault();
            const id = document.getElementById('lembreteId').value;
            const payload = {
                agenda_id: document.getElementById('agenda_id').value,
                mensagem: document.getElementById('mensagem').value.trim(),
                data_lembrete: document.getElementById('data_lembrete').value ? document.getElementById('data_lembrete').value.replace('T', ' ') + ':00' : ''
            };
            if (!payload.agenda_id || !payload.mensagem) { mostrarToast('Preencha agenda e mensagem.', 'erro'); return; }
            const opts = { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(id ? { ...payload, id: parseInt(id) } : payload) };
            try {
                const res = await fetch(API, opts);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro');
                mostrarToast(data.mensagem || 'Salvo!');
                fecharModal();
                carregarLista();
            } catch (err) { mostrarToast(err.message, 'erro'); }
        }

        async function marcarLido(id) {
            try {
                const res = await fetch(API, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id, lido: true }) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro);
                mostrarToast('Marcado como lido!');
                carregarLista();
            } catch (err) { mostrarToast(err.message, 'erro'); }
        }

        function editar(id) { abrirModal(id); }

        async function excluir(btn) {
            const id = btn.getAttribute('data-id');
            abrirConfirm(id, async (idExcluir) => {
                try {
                    const res = await fetch(API + '?id=' + idExcluir, { method: 'DELETE' });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.erro);
                    mostrarToast(data.mensagem || 'Excluído!');
                    carregarLista();
                } catch (err) { mostrarToast(err.message, 'erro'); }
            });
        }

        document.addEventListener('DOMContentLoaded', carregarLista);
    </script>
</body>
</html>
