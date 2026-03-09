<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Agendas - EasyJur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --vermelho-easyjur: #E5293F;
            --vermelho-escuro: #A82130;
            --cinza-escuro: #191919;
            --branco-gelo: #F9F9F9;
            --bege: #F8E3B7;
            --chumbo: #7F919A;
            --cinza: #ACBAC2;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--branco-gelo);
            color: var(--cinza-escuro);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        nav {
            background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro));
            padding: 1rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: opacity 0.2s;
        }
        nav a:hover {
            opacity: 0.9;
            text-decoration: underline;
        }
        nav .logo {
            font-weight: 700;
            font-size: 1.25rem;
        }
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }
        main h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--cinza-escuro);
            margin-bottom: 0.5rem;
        }
        main .subtitulo {
            font-size: 1rem;
            color: var(--chumbo);
            margin-bottom: 2rem;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
        }
        .card .numero {
            font-size: 2rem;
            font-weight: 700;
            color: var(--vermelho-easyjur);
        }
        .card .label {
            font-size: 0.9rem;
            color: var(--chumbo);
            margin-top: 0.25rem;
        }
        .proximas {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .proximas h2 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--cinza-escuro);
        }
        .proximas ul {
            list-style: none;
        }
        .proximas li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .proximas li:last-child { border-bottom: none; }
        .proximas .vazio { color: var(--chumbo); padding: 1rem 0; }
        .relatorios { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .relatorio-card { background: white; border-radius: 8px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .relatorio-card h3 { font-size: 1rem; margin-bottom: 1rem; color: var(--cinza-escuro); }
        .barra-item { margin-bottom: 0.5rem; }
        .barra-item label { display: block; font-size: 0.85rem; margin-bottom: 2px; }
        .barra-item .barra { height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
        .barra-item .barra span { display: block; height: 100%; background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro)); border-radius: 4px; }
        .links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .links a {
            display: inline-block;
            padding: 0.875rem 1.75rem;
            background: linear-gradient(90deg, var(--vermelho-easyjur), var(--vermelho-escuro));
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .links a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 41, 63, 0.35);
        }
        .links a.secundario {
            background: var(--cinza);
        }
        .links a.secundario:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        footer {
            padding: 1rem 2rem;
            background: var(--cinza-escuro);
            color: var(--cinza);
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <ul style="flex-wrap:wrap;gap:1rem;">
            <li><a href="index.php" class="logo">EASYJUR</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="modulos/pessoas/">Pessoas</a></li>
            <li><a href="modulos/agendas/">Agendas</a></li>
            <li><a href="modulos/lembretes/">Lembretes</a></li>
            <li><a href="modulos/tags/">Tags</a></li>
            <li><a href="modulos/logs/">Logs</a></li>
        </ul>
    </nav>
    <main>
        <h1>Bem-vindo ao Sistema de Agendas</h1>
        <p class="subtitulo">Gerencie pessoas e agendas de forma simples.</p>
        <div class="dashboard" id="dashboard">
            <div class="card"><span class="numero" id="totalPessoas">-</span><div class="label">Pessoas</div></div>
            <div class="card"><span class="numero" id="totalAgendas">-</span><div class="label">Agendas</div></div>
            <div class="card"><span class="numero" id="agendasMes">-</span><div class="label">Agendas este mês</div></div>
        </div>
        <div class="relatorios" id="relatorios"></div>
        <div class="proximas">
            <h2>Próximas agendas</h2>
            <ul id="proximasLista"><li class="vazio">Carregando...</li></ul>
        </div>
        <div class="links">
            <a href="modulos/pessoas/">Pessoas</a>
            <a href="modulos/agendas/" class="secundario">Agendas</a>
            <a href="modulos/lembretes/" class="secundario">Lembretes</a>
            <a href="modulos/tags/" class="secundario">Tags</a>
            <a href="modulos/logs/" class="secundario">Logs</a>
        </div>
    </main>
    <footer>
        Sistema de Cadastro de Agendas - EasyJur &copy; Projeto de Estudos
    </footer>
    <script>
        (async function() {
            try {
                const [resP, resA] = await Promise.all([
                    fetch('api/pessoas.php'),
                    fetch('api/agendas.php')
                ]);
                const dataP = await resP.json();
                const dataA = await resA.json();
                const pessoas = dataP.pessoas || [];
                const agendas = dataA.agendas || [];
                document.getElementById('totalPessoas').textContent = pessoas.length;
                document.getElementById('totalAgendas').textContent = agendas.length;
                const hoje = new Date();
                const mesAtual = hoje.getFullYear() + '-' + String(hoje.getMonth() + 1).padStart(2, '0');
                const doMes = agendas.filter(a => (a.data_agenda || '').substring(0, 7) === mesAtual);
                document.getElementById('agendasMes').textContent = doMes.length;
                const comData = agendas.filter(a => a.data_agenda && a.status !== 'cancelado').sort((a,b) => (a.data_agenda + (a.hora_inicio||'')).localeCompare(b.data_agenda + (b.hora_inicio||'')));
                const proximas = comData.slice(0, 5);
                const ul = document.getElementById('proximasLista');
                const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                ul.innerHTML = proximas.length ? proximas.map(a => '<li><span>' + esc(a.titulo) + '</span><span>' + esc(a.data_agenda) + ' ' + esc(a.hora_inicio) + ' - ' + esc(a.pessoa_nome) + '</span></li>').join('') : '<li class="vazio">Nenhuma agenda próxima</li>';

                const rel = document.getElementById('relatorios');
                const meses = [];
                for (let i = 5; i >= 0; i--) {
                    const d = new Date();
                    d.setMonth(d.getMonth() - i);
                    meses.push(d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'));
                }
                const porMes = meses.map(m => ({ mes: m, count: agendas.filter(a => (a.data_agenda || '').substring(0, 7) === m).length }));
                const maxMes = Math.max(1, ...porMes.map(x => x.count));
                const porTipo = {};
                agendas.forEach(a => { const t = a.tipo_nome || 'Sem tipo'; porTipo[t] = (porTipo[t] || 0) + 1; });
                const arrTipo = Object.entries(porTipo).sort((a,b) => b[1]-a[1]).slice(0, 6);
                const maxTipo = Math.max(1, ...arrTipo.map(x => x[1]));
                const porPessoa = {};
                agendas.forEach(a => { const p = a.pessoa_nome || 'Sem pessoa'; porPessoa[p] = (porPessoa[p] || 0) + 1; });
                const arrPessoa = Object.entries(porPessoa).sort((a,b) => b[1]-a[1]).slice(0, 5);
                const maxPessoa = Math.max(1, ...arrPessoa.map(x => x[1]));
                rel.innerHTML = '<div class="relatorio-card"><h3>Agendas por mês</h3>' + porMes.map(x => '<div class="barra-item"><label>' + x.mes + ' (' + x.count + ')</label><div class="barra"><span style="width:' + (100*x.count/maxMes) + '%"></span></div></div>').join('') + '</div><div class="relatorio-card"><h3>Agendas por tipo</h3>' + arrTipo.map(x => '<div class="barra-item"><label>' + esc(x[0]) + ' (' + x[1] + ')</label><div class="barra"><span style="width:' + (100*x[1]/maxTipo) + '%"></span></div></div>').join('') + '</div><div class="relatorio-card"><h3>Agendas por pessoa</h3>' + arrPessoa.map(x => '<div class="barra-item"><label>' + esc(x[0]) + ' (' + x[1] + ')</label><div class="barra"><span style="width:' + (100*x[1]/maxPessoa) + '%"></span></div></div>').join('') + '</div>';
            } catch (e) {
                document.getElementById('proximasLista').innerHTML = '<li class="vazio">Erro ao carregar</li>';
            }
        })();
    </script>
</body>
</html>
