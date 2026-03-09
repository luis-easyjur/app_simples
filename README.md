# Sistema de Cadastro de Agendas - EasyJur

Projeto de estudos para desenvolvedores júnior: sistema monolítico em PHP puro para cadastro de pessoas e agendas, com interface 100% AJAX, modais para cadastro/edição e armazenamento em arquivos JSON.

---

## Índice

1. [Sobre o Projeto](#sobre-o-projeto)
2. [Tecnologias](#tecnologias)
3. [Estrutura do Projeto](#estrutura-do-projeto)
4. [Funcionalidades](#funcionalidades)
5. [Como Funciona](#como-funciona)
6. [Identidade Visual](#identidade-visual)
7. [Tutorial de Inicialização](#tutorial-de-inicialização)

---

## Sobre o Projeto

Este é um sistema simples de cadastro de agendas e pessoas, desenvolvido para refletir o dia a dia da EasyJur e servir como material de estudo para desenvolvedores júnior. O projeto foi pensado para ser:

- **Monolítico:** Todas as páginas em PHP, sem frameworks
- **Didático:** Código organizado por módulos, fácil de entender
- **Prático:** CSS inline e em tags `<style>`, JavaScript em tags `<script>` na própria página
- **Leve:** Sem banco de dados — armazenamento em arquivos JSON

---

## Tecnologias

| Tecnologia | Uso |
|------------|-----|
| PHP 7.4+ | Backend, API, leitura/escrita de JSON |
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização (inline + `<style>`) |
| JavaScript (Vanilla) | AJAX com `fetch()`, modais, validações |
| JSON | Armazenamento de dados (pessoas e agendas) |

---

## Estrutura do Projeto

```
app_simples/
├── index.php                 # Página de boas-vindas
├── README.md                 # Este arquivo
├── data/
│   ├── pessoas.json          # Dados de pessoas (editável)
│   └── agendas.json          # Dados de agendas (editável)
├── api/
│   ├── pessoas.php           # API REST: GET, POST, PUT, DELETE
│   └── agendas.php           # API REST: GET, POST, PUT, DELETE
├── modulos/
│   ├── pessoas/
│   │   └── index.php         # Listagem + modal cadastrar/editar
│   └── agendas/
│       └── index.php         # Listagem + modal cadastrar/editar
└── assets/
    └── (imagens da marca)
```

---

## Funcionalidades

### Página de Boas-Vindas
- Mensagem de boas-vindas
- Links para os módulos Pessoas e Agendas
- Navegação com identidade visual EasyJur

### Módulo Pessoas
- **Listar** pessoas em tabela (carregamento via AJAX)
- **Cadastrar** nova pessoa (modal)
- **Editar** pessoa existente (modal)
- **Excluir** pessoa (com confirmação)

### Módulo Agendas
- **Listar** agendas com nome da pessoa, data e horários
- **Cadastrar** nova agenda vinculada a uma pessoa (modal)
- **Editar** agenda existente (modal)
- **Excluir** agenda (com confirmação)

---

## Como Funciona

### Armazenamento em JSON

Não há conexão com banco de dados. Os dados ficam em arquivos JSON na pasta `data/`:

- **`data/pessoas.json`** — lista de pessoas (id, nome, email, telefone)
- **`data/agendas.json`** — lista de agendas (id, pessoa_id, titulo, data, horários, descrição)

O PHP lê e grava esses arquivos em cada operação (listar, criar, editar, excluir).

### API REST

Os arquivos em `api/` funcionam como endpoints que recebem requisições AJAX e retornam JSON:

| Método | Endpoint | Ação |
|--------|----------|------|
| GET | `api/pessoas.php` | Retorna lista de pessoas |
| POST | `api/pessoas.php` | Cria nova pessoa |
| PUT | `api/pessoas.php` | Edita pessoa existente |
| DELETE | `api/pessoas.php?id=N` | Exclui pessoa |
| GET | `api/agendas.php` | Retorna lista de agendas |
| POST | `api/agendas.php` | Cria nova agenda |
| PUT | `api/agendas.php` | Edita agenda existente |
| DELETE | `api/agendas.php?id=N` | Exclui agenda |

### Fluxo AJAX + Modais

1. Ao abrir a página de Pessoas ou Agendas, o JavaScript faz `fetch()` para buscar os dados
2. A tabela é montada dinamicamente com os dados retornados
3. Ao clicar em "Nova pessoa/agenda", abre um modal com formulário vazio
4. Ao clicar em "Editar", abre o mesmo modal com os dados preenchidos
5. Ao salvar, o JavaScript envia os dados via `fetch()` (POST ou PUT), fecha o modal e recarrega a lista
6. Ao excluir, pede confirmação e envia `fetch()` DELETE, depois atualiza a lista

Tudo acontece sem recarregar a página.

---

## Identidade Visual

O projeto segue a paleta de cores e tipografia da EasyJur:

**Cores:**
- Vermelho EasyJur: `#E5293F`
- Vermelho Escuro: `#A82130`
- Cinza Escuro: `#191919`
- Cinza: `#ACBAC2`
- Chumbo: `#7F919A`
- Branco Gelo: `#F9F9F9`
- Bege: `#F8E3B7`

**Gradientes:**
- Vermelho: `#E5293F` → `#A82130`
- Cinza: `#ACBAC2` → `#191919`

**Fonte:** Montserrat (Google Fonts)

---

## Tutorial de Inicialização

Este tutorial explica como configurar o ambiente do zero no Windows usando WSL (Windows Subsystem for Linux) e rodar o projeto em localhost.

---

### Passo 1: Instalar o WSL

O WSL permite executar um ambiente Linux dentro do Windows, ideal para desenvolvimento PHP.

1. **Abra o PowerShell como Administrador** (clique com o botão direito no menu Iniciar → "Windows PowerShell (Admin)" ou "Terminal (Admin)").

2. **Execute o comando para instalar o WSL:**
   ```powershell
   wsl --install
   ```

3. **Aguarde a instalação.** O Windows baixará o Ubuntu (distribuição padrão) e configurará tudo. Pode levar alguns minutos.

4. **Reinicie o computador** quando solicitado.

5. **Após reiniciar**, o Ubuntu abrirá automaticamente. Crie um **nome de usuário** e **senha** quando pedido. Esses dados serão usados para acessar o Linux.

---

### Passo 2: Atualizar o Ubuntu (WSL)

1. Abra o **Terminal** do Ubuntu (busque por "Ubuntu" no menu Iniciar) ou digite `wsl` no PowerShell.

2. Atualize a lista de pacotes e o sistema:
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```
   Digite sua senha se solicitado.

---

### Passo 3: Instalar o PHP

1. No terminal do Ubuntu, instale o PHP e extensões úteis:
   ```bash
   sudo apt install php php-json php-mbstring -y
   ```

2. Verifique se o PHP foi instalado corretamente:
   ```bash
   php -v
   ```
   Deve aparecer algo como: `PHP 8.x.x` (ou 7.4+).

---

### Passo 4: Acessar o Projeto

1. **Se o projeto já está na sua máquina**, navegue até a pasta. Exemplo:
   ```bash
   cd /home/easyjur/app_simples
   ```

2. **Se você clonou de um repositório**, use:
   ```bash
   cd ~
   git clone <url-do-repositorio> app_simples
   cd app_simples
   ```

3. **Se o projeto está em uma pasta do Windows** (ex: `C:\Users\SeuUsuario\projetos\app_simples`), no WSL você acessa assim:
   ```bash
   cd /mnt/c/Users/SeuUsuario/projetos/app_simples
   ```
   Substitua `SeuUsuario` pelo seu nome de usuário do Windows.

---

### Passo 5: Garantir Permissões na Pasta `data/`

O PHP precisa gravar nos arquivos JSON. Verifique as permissões:

```bash
chmod 755 data
chmod 664 data/*.json
```

Se a pasta `data` não existir ainda, crie e ajuste:

```bash
mkdir -p data
chmod 755 data
```

---

### Passo 6: Iniciar o Servidor PHP

O PHP possui um servidor embutido, ideal para desenvolvimento local.

1. Na pasta do projeto, execute:
   ```bash
   php -S localhost:8000
   ```

2. Você verá uma mensagem como:
   ```
   [Mon Mar  9 12:00:00 2025] PHP 8.x.x Development Server (http://localhost:8000) started
   ```

3. **Mantenha o terminal aberto** — o servidor roda enquanto o terminal estiver ativo.

---

### Passo 7: Acessar o Projeto no Navegador

1. Abra o navegador (Chrome, Edge, Firefox, etc.).

2. Acesse:
   ```
   http://localhost:8000
   ```

3. Você deve ver a página de boas-vindas. Use os links para acessar **Pessoas** e **Agendas**.

---

### Resumo dos Comandos (Para Consulta Rápida)

```bash
# 1. Entrar no WSL (se estiver no PowerShell/CMD)
wsl

# 2. Ir para a pasta do projeto
cd /home/easyjur/app_simples
# ou: cd /mnt/c/Users/SeuUsuario/caminho/do/projeto

# 3. Ajustar permissões (se necessário)
chmod 755 data
chmod 664 data/*.json

# 4. Iniciar o servidor
php -S localhost:8000
```

Depois, acesse **http://localhost:8000** no navegador.

---

### Parar o Servidor

Para parar o servidor PHP, pressione `Ctrl + C` no terminal.

---

### Solução de Problemas

| Problema | Solução |
|----------|---------|
| "php: command not found" | Reinstale o PHP: `sudo apt install php -y` |
| "Permission denied" ao salvar | Ajuste permissões: `chmod 755 data` e `chmod 664 data/*.json` |
| Página em branco | Verifique se está na pasta correta e se `index.php` existe |
| Erro 404 nas APIs | Confirme que a URL está correta: `http://localhost:8000/api/pessoas.php` |
| Porta 8000 em uso | Use outra porta: `php -S localhost:8080` |

---

## Ideias para Evolução do Projeto

- **Validação de formulários:** Campos obrigatórios, formato de email, telefone
- **Mensagens de feedback:** Toast ou alert ao salvar/excluir com sucesso ou erro
- **Busca/filtro:** Filtrar pessoas ou agendas na listagem
- **Paginação:** Se a lista crescer, dividir em páginas
- **Migração para MySQL:** Trocar JSON por banco de dados como próximo passo de estudo
- **Autenticação:** Tela de login simples para proteger o sistema

---

## Licença

Projeto interno EasyJur — uso educacional.
