<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendas - Sistema de Agendas</title>
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
        main { flex: 1; padding: 2rem; max-width: 1100px; margin: 0 auto; width: 100%; }
        main h1 { font-size: 1.75rem; margin-bottom: 1rem; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .toolbar-left { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .toolbar-right { display: flex; gap: 0.5rem; }
        .busca, .filtro { padding: 0.4rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; }
        .busca { width: 200px; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; font-family: inherit; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
        .btn:hover { transform: translateY(-1px); opacity: 0.95; }
        .btn-primario { background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); color: white; }
        .btn-editar { background: var(--chumbo); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-excluir { background: var(--vermelho-escuro); color: white; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-duplicar { background: var(--cinza); color: var(--cinza-escuro); padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-cancelar { background: var(--cinza); color: var(--cinza-escuro); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: var(--cinza); color: white; font-weight: 600; cursor: pointer; user-select: none; }
        th:hover { opacity: 0.9; }
        th.sort-asc::after { content: ' ▲'; font-size: 0.7em; }
        th.sort-desc::after { content: ' ▼'; font-size: 0.7em; }
        tr:hover { background: #fafafa; }
        .acoes { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-agendado { background: #dbeafe; color: #1e40af; }
        .badge-realizado { background: #d1fae5; color: #065f46; }
        .badge-cancelado { background: #fee2e2; color: #991b1b; }
        .badge-adiado { background: #fef3c7; color: #92400e; }
        .loading, .vazio, .erro { text-align: center; padding: 2rem; color: var(--chumbo); }
        .erro { color: var(--vermelho-easyjur); }
        .toast { position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.5rem; border-radius: 8px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s ease; }
        .toast.sucesso { background: #22c55e; color: white; }
        .toast.erro { background: var(--vermelho-easyjur); color: white; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: -1; align-items: center; justify-content: center; }
        .modal-overlay.ativo { display: flex; }
        .modal { background: white; border-radius: 8px; padding: 2.5rem; width: 100%; max-width: 720px; min-height: 70vh; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .modal h2 { margin-bottom: 1.5rem; font-size: 1.35rem; }
        .modal .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.5rem; }
        .modal .form-grid .form-group-full { grid-column: 1 / -1; }
        .form-group { margin-bottom: 0.5rem; }
        .form-group.form-group-full { margin-bottom: 1rem; }
        @media (max-width: 600px) {
            .modal .form-grid { grid-template-columns: 1fr; }
        }
        .form-group label { display: block; margin-bottom: 0.35rem; font-weight: 500; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--cinza); border-radius: 6px; font-family: inherit; font-size: 0.95rem; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--vermelho-easyjur); }
        .form-group textarea { min-height: 120px; resize: vertical; max-length: 500; }
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
            <li><a href="../pessoas/">Pessoas</a></li>
            <li><a href="index.php">Agendas</a></li>
            <li><a href="../lembretes/">Lembretes</a></li>
            <li><a href="../tags/">Tags</a></li>
            <li><a href="../logs/">Logs</a></li>
        </ul>
    </nav>
    <main>
        <h1>Agendas</h1>
        <div class="toolbar">
            <div class="toolbar-left">
                <input type="text" id="busca" class="busca" placeholder="Buscar por título ou pessoa...">
                <select id="filtroStatus" class="filtro">
                    <option value="">Todos os status</option>
                    <option value="agendado">Agendado</option>
                    <option value="realizado">Realizado</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="adiado">Adiado</option>
                </select>
                <select id="filtroTag" class="filtro">
                    <option value="">Todas as tags</option>
                </select>
                <a href="../../api/exportar.php?tipo=agendas" class="btn btn-cancelar" download="agendas.csv">Exportar CSV</a>
            </div>
            <div class="toolbar-right">
                <button class="btn btn-primario" onclick="abrirModal()">Nova agenda</button>
            </div>
        </div>
        <div id="conteudo"><div class="loading"><span class="spinner"></span>Carregando...</div></div>
        <div class="paginacao" id="paginacao" style="display:none;"></div>
    </main>
    <div class="modal-overlay" id="modalOverlay" onclick="fecharModalSeOverlay(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <h2 id="modalTitulo">Nova agenda</h2>
            <form id="formAgenda" onsubmit="salvar(event)">
                <input type="hidden" id="agendaId" value="">
                <div class="form-grid">
                <div class="form-group">
                    <label for="pessoa_id">Pessoa *</label>
                    <select id="pessoa_id" required><option value="">Selecione...</option></select>
                </div>
                <div class="form-group">
                    <label for="tipo_id">Tipo</label>
                    <select id="tipo_id"><option value="">Selecione...</option></select>
                </div>
                <div class="form-group">
                    <label for="tags_select">Tags</label>
                    <select id="tags_select" multiple size="4" style="padding:0.5rem;min-height:90px;width:100%;">
                        <option value="">Carregando...</option>
                    </select>
                    <small style="color:var(--chumbo);font-size:0.75rem;">Ctrl+clique para múltiplas</small>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status">
                        <option value="agendado">Agendado</option>
                        <option value="realizado">Realizado</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="adiado">Adiado</option>
                    </select>
                </div>
                <div class="form-group form-group-full">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" required placeholder="Ex: Reunião de alinhamento" maxlength="150">
                    <div class="contador"><span id="tituloContador">0</span>/150</div>
                </div>
                <div class="form-group">
                    <label for="data_agenda">Data</label>
                    <input type="date" id="data_agenda">
                </div>
                <div class="form-group">
                    <label for="hora_inicio">Hora início</label>
                    <input type="time" id="hora_inicio" value="09:00">
                </div>
                <div class="form-group">
                    <label for="hora_fim">Hora fim</label>
                    <input type="time" id="hora_fim" value="10:00">
                </div>
                <div class="form-group form-group-full">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" placeholder="Detalhes da agenda" maxlength="500"></textarea>
                    <div class="contador"><span id="descContador">0</span>/500</div>
                </div>
                <div class="form-group form-group-full" id="comentariosSection" style="display:none;">
                    <label>Comentários</label>
                    <div id="comentariosLista"></div>
                    <div style="margin-top:0.5rem;">
                        <input type="text" id="novoComentario" placeholder="Adicionar comentário..." style="width:100%;padding:0.5rem;">
                        <button type="button" class="btn btn-primario" style="margin-top:0.5rem;" onclick="adicionarComentario()">Adicionar</button>
                    </div>
                </div>
                </div>
                <div class="modal-botoes" style="margin-top:1.5rem;">
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
        const API_AGENDAS = '../../api/agendas.php';
        const API_PESSOAS = '../../api/pessoas.php';
        const API_TIPOS = '../../api/tipos_agenda.php';
        const API_TAGS = '../../api/tags.php';
        const API_COMENTARIOS = '../../api/comentarios.php';
        const ITENS_POR_PAGINA = 10;
        let dadosCompletos = [];
        let ordemCol = 'data_agenda';
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

        function escapar(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        function formatarData(d) {
            if (!d) return '-';
            const [y, m, day] = String(d).split('-');
            return day && m && y ? day + '/' + m + '/' + y : d;
        }

        function badgeStatus(s) {
            const m = { agendado: 'Agendado', realizado: 'Realizado', cancelado: 'Cancelado', adiado: 'Adiado' };
            const c = 'badge-' + (s || 'agendado');
            return '<span class="badge ' + c + '">' + escapar(m[s] || s) + '</span>';
        }

        document.getElementById('busca').addEventListener('input', function() {
            clearTimeout(this._debounce);
            this._debounce = setTimeout(() => aplicarFiltros(), 300);
        });
        document.getElementById('filtroStatus').addEventListener('change', () => aplicarFiltros());
        document.getElementById('filtroTag').addEventListener('change', () => aplicarFiltros());

        document.getElementById('titulo').addEventListener('input', function() {
            document.getElementById('tituloContador').textContent = this.value.length;
        });
        document.getElementById('descricao').addEventListener('input', function() {
            document.getElementById('descContador').textContent = this.value.length;
        });

        const hoje = new Date().toISOString().slice(0, 10);
        document.getElementById('data_agenda').setAttribute('min', hoje);

        function aplicarFiltros() {
            const busca = document.getElementById('busca').value.toLowerCase().trim();
            const statusFiltro = document.getElementById('filtroStatus').value;
            const tagFiltro = document.getElementById('filtroTag').value;
            let filtrados = dadosCompletos;
            if (busca) {
                filtrados = filtrados.filter(a =>
                    (a.titulo || '').toLowerCase().includes(busca) ||
                    (a.pessoa_nome || '').toLowerCase().includes(busca)
                );
            }
            if (statusFiltro) filtrados = filtrados.filter(a => (a.status || '') === statusFiltro);
            if (tagFiltro) {
                const tagId = parseInt(tagFiltro);
                filtrados = filtrados.filter(a => ((a.tag_ids || [])).includes(tagId));
            }
            filtrados.sort((a, b) => {
                const va = (a[ordemCol] || '').toString();
                const vb = (b[ordemCol] || '').toString();
                return ordemDir * (ordemCol === 'data_agenda' ? va.localeCompare(vb) : va.toLowerCase().localeCompare(vb.toLowerCase()));
            });
            paginaAtual = 1;
            renderizarTabela(filtrados);
        }

        function ordenar(col) {
            if (ordemCol === col) ordemDir *= -1;
            else { ordemCol = col; ordemDir = 1; }
            aplicarFiltros();
        }

        function renderizarTabela(agendas) {
            const container = document.getElementById('conteudo');
            const pagEl = document.getElementById('paginacao');
            if (!agendas.length) {
                container.innerHTML = '<div class="vazio">Nenhuma agenda encontrada.</div>';
                pagEl.style.display = 'none';
                return;
            }
            const total = agendas.length;
            const inicio = (paginaAtual - 1) * ITENS_POR_PAGINA;
            const fim = Math.min(inicio + ITENS_POR_PAGINA, total);
            const pagina = agendas.slice(inicio, fim);
            const totalPag = Math.ceil(total / ITENS_POR_PAGINA);
            let html = '<table><thead><tr>';
            const th = (col, label) => '<th class="' + (ordemCol === col ? 'sort-' + (ordemDir > 0 ? 'asc' : 'desc') : '') + '" onclick="ordenar(\'' + col + '\')">' + escapar(label) + '</th>';
            html += th('titulo','Título') + th('tipo_nome','Tipo') + '<th>Tags</th><th>Pessoa</th>' + th('data_agenda','Data') + '<th>Horário</th>' + '<th>Status</th>' + '<th>Ações</th></tr></thead><tbody>';
            pagina.forEach(a => {
                const horario = (a.hora_inicio || '') + ' - ' + (a.hora_fim || '');
                const tagsHtml = (a.tags || []).map(t => '<span class="badge badge-tag" style="background:' + escapar(t.cor) + ';color:white;margin:0 2px;padding:2px 6px;border-radius:4px;font-size:0.7rem;">' + escapar(t.nome) + '</span>').join('');
                html += '<tr><td>' + escapar(a.titulo) + '</td><td>' + escapar(a.tipo_nome) + '</td><td>' + (tagsHtml || '-') + '</td><td>' + escapar(a.pessoa_nome) + '</td><td>' + formatarData(a.data_agenda) + '</td><td>' + escapar(horario) + '</td><td>' + badgeStatus(a.status) + '</td><td class="acoes"><button class="btn btn-editar" onclick="editar(' + a.id + ')">Editar</button><button class="btn btn-duplicar" onclick="duplicar(' + a.id + ')">Duplicar</button><button class="btn btn-excluir" onclick="excluir(this)" data-id="' + a.id + '" data-titulo="' + escapar(a.titulo).replace(/"/g, '&quot;') + '">Excluir</button></td></tr>';
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
                const res = await fetch(API_AGENDAS);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro ao carregar');
                dadosCompletos = data.agendas || [];
                aplicarFiltros();
            } catch (err) {
                el.innerHTML = '<div class="erro">Erro ao carregar: ' + escapar(err.message) + '</div>';
            }
        }

        async function carregarPessoasNoSelect() {
            const select = document.getElementById('pessoa_id');
            const valorAtual = select.value;
            select.innerHTML = '<option value="">Selecione...</option>';
            try {
                const res = await fetch(API_PESSOAS);
                const data = await res.json();
                (data.pessoas || []).forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nome || 'Sem nome';
                    select.appendChild(opt);
                });
                select.value = valorAtual || '';
            } catch (e) { console.error(e); }
        }

        async function carregarTiposNoSelect() {
            const select = document.getElementById('tipo_id');
            const valorAtual = select.value;
            select.innerHTML = '<option value="">Selecione...</option>';
            try {
                const res = await fetch(API_TIPOS);
                const data = await res.json();
                (data.tipos || []).forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.nome || '';
                    select.appendChild(opt);
                });
                select.value = valorAtual || '';
            } catch (e) { console.error(e); }
        }

        let todasTags = [];
        async function carregarTags() {
            try {
                const res = await fetch(API_TAGS);
                const data = await res.json();
                todasTags = data.tags || [];
                const sel = document.getElementById('filtroTag');
                const v = sel.value;
                sel.innerHTML = '<option value="">Todas as tags</option>';
                todasTags.forEach(t => {
                    const o = document.createElement('option');
                    o.value = t.id;
                    o.textContent = t.nome || '';
                    sel.appendChild(o);
                });
                sel.value = v || '';
            } catch (e) {}
        }

        function renderizarTagsSelect(selecionados = []) {
            const sel = document.getElementById('tags_select');
            sel.innerHTML = '';
            if (!todasTags.length) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Nenhuma tag cadastrada';
                sel.appendChild(opt);
                return;
            }
            todasTags.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nome || '';
                opt.selected = selecionados.includes(t.id);
                sel.appendChild(opt);
            });
        }

        function getTagIdsSelecionados() {
            const ids = [];
            const sel = document.getElementById('tags_select');
            for (let i = 0; i < sel.options.length; i++) {
                const v = parseInt(sel.options[i].value);
                if (sel.options[i].selected && !isNaN(v)) ids.push(v);
            }
            return ids;
        }

        async function carregarComentarios(agendaId) {
            const lista = document.getElementById('comentariosLista');
            try {
                const res = await fetch(API_COMENTARIOS + '?agenda_id=' + agendaId);
                const data = await res.json();
                const coms = data.comentarios || [];
                lista.innerHTML = coms.length ? coms.map(c => '<div style="padding:0.5rem;background:#f5f5f5;border-radius:6px;margin-bottom:0.5rem;font-size:0.9rem;"><span>' + escapar(c.texto) + '</span> <small style="color:#666">' + (c.criado_em || '').replace('T',' ') + '</small> <button type="button" class="btn btn-excluir" style="float:right;padding:2px 6px;font-size:0.75rem" onclick="excluirComentario(' + c.id + ')">Excluir</button></div>').join('') : '<p style="color:#666;font-size:0.9rem">Nenhum comentário.</p>';
            } catch (e) { lista.innerHTML = '<p style="color:#666">Erro ao carregar.</p>'; }
        }

        async function adicionarComentario() {
            const agendaId = document.getElementById('agendaId').value;
            const texto = document.getElementById('novoComentario').value.trim();
            if (!agendaId || !texto) return;
            try {
                const res = await fetch(API_COMENTARIOS, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ agenda_id: parseInt(agendaId), texto }) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro);
                document.getElementById('novoComentario').value = '';
                carregarComentarios(agendaId);
                mostrarToast('Comentário adicionado!');
            } catch (err) { mostrarToast(err.message, 'erro'); }
        }

        async function excluirComentario(id) {
            try {
                const res = await fetch(API_COMENTARIOS + '?id=' + id, { method: 'DELETE' });
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro);
                carregarComentarios(document.getElementById('agendaId').value);
                mostrarToast('Comentário excluído!');
            } catch (err) { mostrarToast(err.message, 'erro'); }
        }

        async function abrirModal(id = null) {
            document.getElementById('modalTitulo').textContent = id ? 'Editar agenda' : 'Nova agenda';
            document.getElementById('agendaId').value = id || '';
            document.getElementById('titulo').value = '';
            document.getElementById('data_agenda').value = hoje;
            document.getElementById('data_agenda').setAttribute('min', hoje);
            document.getElementById('hora_inicio').value = '09:00';
            document.getElementById('hora_fim').value = '10:00';
            document.getElementById('status').value = 'agendado';
            document.getElementById('descricao').value = '';
            document.getElementById('tituloContador').textContent = '0';
            document.getElementById('descContador').textContent = '0';
            document.getElementById('novoComentario').value = '';
            document.getElementById('comentariosSection').style.display = id ? 'block' : 'none';
            await carregarPessoasNoSelect();
            await carregarTiposNoSelect();
            if (todasTags.length === 0) await carregarTags();
            if (id) {
                const a = dadosCompletos.find(x => x.id == id);
                if (a) {
                    document.getElementById('titulo').value = a.titulo || '';
                    document.getElementById('pessoa_id').value = a.pessoa_id || '';
                    document.getElementById('tipo_id').value = a.tipo_id || '';
                    document.getElementById('data_agenda').value = a.data_agenda || '';
                    document.getElementById('hora_inicio').value = (a.hora_inicio || '09:00').substring(0, 5);
                    document.getElementById('hora_fim').value = (a.hora_fim || '10:00').substring(0, 5);
                    document.getElementById('status').value = a.status || 'agendado';
                    document.getElementById('descricao').value = a.descricao || '';
                    document.getElementById('tituloContador').textContent = (a.titulo || '').length;
                    document.getElementById('descContador').textContent = (a.descricao || '').length;
                    renderizarTagsSelect(a.tag_ids || []);
                    carregarComentarios(id);
                }
            } else {
                renderizarTagsSelect([]);
            }
            document.getElementById('modalOverlay').classList.add('ativo');
        }

        function fecharModal() { document.getElementById('modalOverlay').classList.remove('ativo'); }
        function fecharModalSeOverlay(e) { if (e.target.id === 'modalOverlay') fecharModal(); }

        function abrirConfirm(id, titulo, callback) {
            document.getElementById('confirmMsg').textContent = 'Excluir "' + titulo + '"?';
            document.getElementById('confirmBtn').onclick = () => { fecharConfirm(); callback(id); };
            document.getElementById('modalConfirm').classList.add('ativo');
        }
        function fecharConfirm() { document.getElementById('modalConfirm').classList.remove('ativo'); }
        function fecharConfirmSeOverlay(e) { if (e.target.id === 'modalConfirm') fecharConfirm(); }

        async function salvar(e) {
            e.preventDefault();
            const dataVal = document.getElementById('data_agenda').value;
    
            if (dataVal && dataVal < hoje) {
                mostrarToast('Data não pode ser no passado.', 'erro');
                return;
            }
            const id = document.getElementById('agendaId').value;
            const payload = {
                pessoa_id: document.getElementById('pessoa_id').value,
                tipo_id: document.getElementById('tipo_id').value || 0,
                tag_ids: getTagIdsSelecionados(),
                titulo: document.getElementById('titulo').value.trim(),
                data_agenda: dataVal || hoje,
                hora_inicio: document.getElementById('hora_inicio').value || '09:00',
                hora_fim: document.getElementById('hora_fim').value || '10:00',
                status: document.getElementById('status').value || 'agendado',
                descricao: document.getElementById('descricao').value.trim()
            };
            if (!payload.titulo || !payload.pessoa_id) {
                mostrarToast('Título e pessoa são obrigatórios.', 'erro');
                return;
            }
            const opts = { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(id ? { ...payload, id: parseInt(id) } : payload) };
            try {
                const res = await fetch(API_AGENDAS, opts);
                const data = await res.json();
                if (!res.ok) throw new Error(data.erro || 'Erro ao salvar');
                mostrarToast(data.mensagem || 'Salvo com sucesso!');
                fecharModal();
                carregarLista();
            } catch (err) { mostrarToast(err.message || 'Erro ao salvar', 'erro'); }
        }

        function editar(id) { abrirModal(id); }

        async function duplicar(id) {
            const a = dadosCompletos.find(x => x.id == id);
            if (!a) return;
            document.getElementById('agendaId').value = '';
            document.getElementById('modalTitulo').textContent = 'Nova agenda (duplicar)';
            document.getElementById('titulo').value = (a.titulo || '') + ' (cópia)';
            document.getElementById('data_agenda').value = hoje;
            document.getElementById('hora_inicio').value = (a.hora_inicio || '09:00').substring(0, 5);
            document.getElementById('hora_fim').value = (a.hora_fim || '10:00').substring(0, 5);
            document.getElementById('status').value = 'agendado';
            document.getElementById('descricao').value = a.descricao || '';
            document.getElementById('tituloContador').textContent = document.getElementById('titulo').value.length;
            document.getElementById('descContador').textContent = (a.descricao || '').length;
            await carregarPessoasNoSelect();
            await carregarTiposNoSelect();
            if (todasTags.length === 0) await carregarTags();
            document.getElementById('pessoa_id').value = a.pessoa_id || '';
            document.getElementById('tipo_id').value = a.tipo_id || '';
            renderizarTagsSelect(a.tag_ids || []);
            document.getElementById('comentariosSection').style.display = 'none';
            document.getElementById('modalOverlay').classList.add('ativo');
        }

        async function excluir(btn) {
            const id = btn.getAttribute('data-id');
            const titulo = btn.getAttribute('data-titulo') || 'esta agenda';
            abrirConfirm(id, titulo, async (idExcluir) => {
                try {
                    const res = await fetch(API_AGENDAS + '?id=' + idExcluir, { method: 'DELETE' });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.erro || 'Erro ao excluir');
                    mostrarToast(data.mensagem || 'Excluído com sucesso!');
                    carregarLista();
                } catch (err) { mostrarToast(err.message || 'Erro ao excluir', 'erro'); }
            });
        }

        document.addEventListener('DOMContentLoaded', () => { carregarTags(); carregarLista(); });
    </script>
</body>
</html>
