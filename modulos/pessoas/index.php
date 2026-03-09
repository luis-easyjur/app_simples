<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pessoas - Sistema de Agendas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --vermelho-easyjur: #E5293F; --vermelho-escuro: #A82130; --cinza-escuro: #191919; --branco-gelo: #F9F9F9; --chumbo: #7F919A; --cinza: #ACBAC2; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: var(--branco-gelo); color: var(--cinza-escuro); min-height: 100vh; display: flex; flex-direction: column; }
        nav { background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); padding: 1rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        nav ul { list-style: none; display: flex; gap: 2rem; align-items: center; }
        nav a { color: white; text-decoration: none; font-weight: 500; }
        nav a:hover { opacity: 0.9; text-decoration: underline; }
        nav .logo { font-weight: 700; font-size: 1.25rem; }
        main { flex: 1; padding: 2rem; max-width: 950px; margin: 0 auto; width: 100%; }
        main h1 { font-size: 1.75rem; margin-bottom: 1rem; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .toolbar-left { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .toolbar-right { display: flex; gap: 0.5rem; }
        .busca { padding: 0.4rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; width: 200px; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; font-family: inherit; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
        .btn:hover { transform: translateY(-1px); opacity: 0.95; }
        .btn-primario { background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); color: white; }
        .btn-editar { background: var(--chumbo); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-excluir { background: var(--vermelho-escuro); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-cancelar { background: var(--cinza); color: var(--cinza-escuro); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--cinza); color: white; font-weight: 600; cursor: pointer; user-select: none; }
        th:hover { opacity: 0.9; }
        th.sort-asc::after { content: ' ▲'; font-size: 0.7em; }
        th.sort-desc::after { content: ' ▼'; font-size: 0.7em; }
        tr:hover { background: #fafafa; }
        .acoes { display: flex; gap: 0.5rem; }
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
        .form-group input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; font-size: 0.95rem; }
        .form-group input:focus { outline: none; border-color: var(--vermelho-easyjur); }
        .form-group input.invalido { border-color: var(--vermelho-easyjur); }
        .form-group .contador { font-size: 0.75rem; color: var(--chumbo); margin-top: 0.25rem; }
        .modal-botoes { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .modal-botoes .btn { flex: 1; }
        .modal-confirm-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1100; align-items: center; justify-content: center; }
        .modal-confirm-overlay.ativo { display: flex; }
        .modal-confirm { background: white; border-radius: 8px; padding: 1.5rem; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .modal-confirm p { margin-bottom: 1rem; }
        .modal-confirm .botoes { display: flex; gap: 0.5rem; justify-content: flex-end; }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 2px solid #eee; border-top-color: var(--vermelho-easyjur); border-radius: 50%; animation: spin 0.8s linear infinite; vertical-align: middle; margin-right: 0.5rem; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .paginacao { display: flex; gap: 0.5rem; align-items: center; margin-top: 1rem; flex-wrap: wrap; }
        .paginacao button { padding: 0.35rem 0.75rem; border: 1px solid var(--cinza); background: white; border-radius: 6px; cursor: pointer; font-family: inherit; }
        .paginacao button:hover:not(:disabled) { background: #f5f5f5; }
        .paginacao button:disabled { opacity: 0.5; cursor: not-allowed; }
        .paginacao .info { font-size: 0.9rem; color: var(--chumbo); }
        footer { padding: 1rem 2rem; background: var(--cinza-escuro); color: var(--cinza); font-size: 0.875rem; text-align: center; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="../../index.php" class="logo">EASYJUR</a></li>
            <li><a href="../../index.php">Home</a></li>
            <li><a href="index.php">Pessoas</a></li>
            <li><a href="../agendas/">Agendas</a></li>
            <li><a href="../lembretes/">Lembretes</a></li>
            <li><a href="../tags/">Tags</a></li>
            <li><a href="../logs/">Logs</a></li>
        </ul>
    </nav>
    <main>
        <h1>Pessoas</h1>
        <div class="toolbar">
            <div class="toolbar-left">
                <input type="text" id="busca" class="busca" placeholder="Buscar por nome ou email...">
                <a href="../../api/exportar.php?tipo=pessoas" class="btn btn-cancelar" download="pessoas.csv">Exportar CSV</a>
            </div>
            <div class="toolbar-right">
                <button class="btn btn-primario" onclick="abrirModal()">Nova pessoa</button>
            </div>
        </div>
        <div id="conteudo"><div class="loading"><span class="spinner"></span>Carregando...</div></div>
        <div class="paginacao" id="paginacao" style="display:none;"></div>
    </main>
    <div class="modal-overlay" id="modalOverlay" onclick="fecharModalSeOverlay(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <h2 id="modalTitulo">Nova pessoa</h2>
            <form id="formPessoa" onsubmit="salvar(event)">
                <input type="hidden" id="pessoaId" value="">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" required placeholder="Nome completo" maxlength="100">
                    <div class="contador"><span id="nomeContador">0</span>/100</div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" placeholder="email@exemplo.com">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" placeholder="(11) 98765-4321">
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
            <p id="confirmMsg">Excluir este registro?</p>
            <div class="botoes">
                <button class="btn btn-cancelar" onclick="fecharConfirm()">Cancelar</button>
                <button class="btn btn-excluir" id="confirmBtn">Excluir</button>
            </div>
        </div>
    </div>
    <footer>Sistema de Cadastro de Agendas - EasyJur &copy; Projeto de Estudos</footer>

    <script>
        const API = '../api/pessoas.php';
        const ITENS_POR_PAGINA = 10;
        let dadosCompletos = [];
        let ordemCol = 'nome';
        let ordemDir = 1;
        let paginaAtual = 1;

        function mostrarToast(mensagem, tipo = 'sucesso') {
            const existente = document.querySelector('.toast');
            if (existente) existente.remove();
            const toast = document.createElement('div');
            toast.className = 'toast ' + tipo;
            toast.textContent = mensagem;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function mascaraTelefone(v) {
            v = v.replace(/\D/g, '');
            if (v.length <= 2) return v ? '(' + v : '';
            if (v.length <= 6) return '(' + v.slice(0,2) + ') ' + v.slice(2);
            return '(' + v.slice(0,2) + ') ' + v.slice(2,6) + '-' + v.slice(6,10);
        }

        function validarEmail(email) {
            if (!email) return true;
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        document.getElementById('telefone').addEventListener('input', function() {
            this.value = mascaraTelefone(this.value);
        });

        document.getElementById('nome').addEventListener('input', function() {
            document.getElementById('nomeContador').textContent = this.value.length;
        });

        document.getElementById('busca').addEventListener('input', function() {
            clearTimeout(this._debounce);
            this._debounce = setTimeout(() => aplicarFiltros(), 300);
        });

        function aplicarFiltros() {
            const busca = document.getElementById('busca').value.toLowerCase().trim();
            let filtrados = dadosCompletos;
            if (busca) {
                filtrados = dadosCompletos.filter(p =>
                    (p.nome || '').toLowerCase().includes(busca) ||
                    (p.email || '').toLowerCase().includes(busca)
                );
            }
            filtrados.sort((a, b) => {
                const va = (a[ordemCol] || '').toString().toLowerCase();
                const vb = (b[ordemCol] || '').toString().toLowerCase();
                return ordemDir * va.localeCompare(vb);
            });
            paginaAtual = 1;
            renderizarTabela(filtrados);
        }

        function ordenar(col) {
            if (ordemCol === col) ordemDir *= -1;
            else { ordemCol = col; ordemDir = 1; }
            aplicarFiltros();
        }

        function escapar(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        function renderizarTabela(pessoas) {
            const container = document.getElementById('conteudo');
            const pagEl = document.getElementById('paginacao');
            if (!pessoas.length) {
                container.innerHTML = '<div class="vazio">Nenhuma pessoa encontrada.</div>';
                pagEl.style.display = 'none';
                return;
            }
            const total = pessoas.length;
            const inicio = (paginaAtual - 1) * ITENS_POR_PAGINA;
            const fim = Math.min(inicio + ITENS_POR_PAGINA, total);
            const pagina = pessoas.slice(inicio, fim);
            const totalPag = Math.ceil(total / ITENS_POR_PAGINA);
            let html = '<table><thead><tr>';
            const th = (col, label) => '<th class="' + (ordemCol === col ? 'sort-' + (ordemDir > 0 ? 'asc' : 'desc') : '') + '" onclick="ordenar(\'' + col + '\')">' + escapar(label) + '</th>';
            html += th('nome','Nome') + th('email','E-mail') + th('telefone','Telefone') + '<th>Ações</th></tr></thead><tbody>';
            pagina.forEach(p => {
                html += '<tr><td>' + escapar(p.nome) + '</td><td>' + escapar(p.email) + '</td><td>' + escapar(p.telefone) + '</td><td class="acoes"><button class="btn btn-editar" onclick="editar(' + p.id + ')">Editar</button><button class="btn btn-excluir" onclick="excluir(this)" data-id="' + p.id + '" data-nome="' + escapar(p.nome).replace(/"/g, '&quot;') + '">Excluir</button></td></tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;
            pagEl.innerHTML = '<button onclick="paginaAtual--; aplicarFiltros();" ' + (paginaAtual <= 1 ? 'disabled' : '') + '>Anterior</button><span class="info">Página ' + paginaAtual + ' de ' + totalPag + ' (' + total + ' registros)</span><button onclick="paginaAtual++; aplicarFiltros();" ' + (paginaAtual >= totalPag ? 'disabled' : '') + '>Próxima</button>';
            pagEl.style.display = totalPag > 1 ? 'flex' : 'none';
        }

        async function carregarLista() {
            const el = document.getElementById('conteudo');
            el.innerHTML = '<div class="loading"><span class="spinner"></span>Carregando...</div>';
            try {
                const res = await fetch(API);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro ao carregar');
                dadosCompletos = data.pessoas || [];
                aplicarFiltros();
            } catch (err) {
                el.innerHTML = '<div class="erro">Erro ao carregar: ' + escapar(err.message) + '</div>';
            }
        }

        function abrirModal(id = null) {
            document.getElementById('modalTitulo').textContent = id ? 'Editar pessoa' : 'Nova pessoa';
            document.getElementById('pessoaId').value = id || '';
            document.getElementById('nome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('telefone').value = '';
            document.getElementById('nomeContador').textContent = '0';
            document.getElementById('email').classList.remove('invalido');
            if (id) {
                const p = dadosCompletos.find(x => x.id == id);
                if (p) {
                    document.getElementById('nome').value = p.nome || '';
                    document.getElementById('email').value = p.email || '';
                    document.getElementById('telefone').value = p.telefone || '';
                    document.getElementById('nomeContador').textContent = (p.nome || '').length;
                }
            }
            document.getElementById('modalOverlay').classList.add('ativo');
        }

        function fecharModal() { document.getElementById('modalOverlay').classList.remove('ativo'); }
        function fecharModalSeOverlay(e) { if (e.target.id === 'modalOverlay') fecharModal(); }

        function abrirConfirm(id, nome, callback) {
            document.getElementById('confirmMsg').textContent = 'Excluir "' + nome + '"?';
            document.getElementById('confirmBtn').onclick = () => { fecharConfirm(); callback(id); };
            document.getElementById('modalConfirm').classList.add('ativo');
        }
        function fecharConfirm() { document.getElementById('modalConfirm').classList.remove('ativo'); }
        function fecharConfirmSeOverlay(e) { if (e.target.id === 'modalConfirm') fecharConfirm(); }

        async function salvar(e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            if (!validarEmail(email)) {
                document.getElementById('email').classList.add('invalido');
                mostrarToast('E-mail inválido.', 'erro');
                return;
            }
            document.getElementById('email').classList.remove('invalido');
            const id = document.getElementById('pessoaId').value;
            const payload = {
                nome: document.getElementById('nome').value.trim(),
                email: email,
                telefone: document.getElementById('telefone').value.trim()
            };
            if (!payload.nome) { mostrarToast('Nome é obrigatório.', 'erro'); return; }
            const opts = { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(id ? { ...payload, id: parseInt(id) } : payload) };
            try {
                const res = await fetch(API, opts);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro ao salvar');
                mostrarToast(data.mensagem || 'Salvo com sucesso!');
                fecharModal();
                carregarLista();
            } catch (err) { mostrarToast(err.message || 'Erro ao salvar', 'erro'); }
        }

        function editar(id) { abrirModal(id); }

        async function excluir(btn) {
            const id = btn.getAttribute('data-id');
            const nome = btn.getAttribute('data-nome') || 'esta pessoa';
            abrirConfirm(id, nome, async (idExcluir) => {
                try {
                    const res = await fetch(API + '?id=' + idExcluir, { method: 'DELETE' });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.erro || 'Erro ao excluir');
                    mostrarToast(data.mensagem || 'Excluído com sucesso!');
                    carregarLista();
                } catch (err) { mostrarToast(err.message || 'Erro ao excluir', 'erro'); }
            });
        }

        document.addEventListener('DOMContentLoaded', carregarLista);
    </script>
</body>
</html>
