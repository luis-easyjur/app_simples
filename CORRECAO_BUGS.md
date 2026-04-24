# Guia de Correção dos 7 Bugs

Este documento contém a solução para os 7 bugs introduzidos no projeto. **Não leia antes de tentar corrigir!**

---

## Bug 1 – PHP (api/agendas.php)

**Sintoma:** Ao cadastrar uma nova agenda com data de **hoje** ou **futura**, o sistema retorna erro "Data não pode ser no passado". Datas no passado são aceitas.

**Causa:** Operador de comparação invertido na validação da data.

**Local:** Linha ~75 em `api/agendas.php`

**Correção:** Trocar `>` por `<` na condição:

```php
// ERRADO (bug):
if (strtotime($dataAgenda) > strtotime(date('Y-m-d'))) {

// CORRETO:
if (strtotime($dataAgenda) < strtotime(date('Y-m-d'))) {
```

**Explicação:** Queremos rejeitar quando a data da agenda é **anterior** a hoje (`<`), não quando é **posterior** (`>`).

---

## Bug 2 – PHP (api/pessoas.php)

**Sintoma:** E-mails inválidos (ex: "teste", "abc@") são aceitos no cadastro e edição de pessoas. A validação não funciona.

**Causa:** Uso de `FILTER_SANITIZE_EMAIL` em vez de `FILTER_VALIDATE_EMAIL`. O sanitize retorna uma string (nunca `false`), então a condição nunca rejeita.

**Local:** Linhas ~42 e ~79 em `api/pessoas.php` (POST e PUT)

**Correção:** Trocar `FILTER_SANITIZE_EMAIL` por `FILTER_VALIDATE_EMAIL`:

```php
// ERRADO (bug):
if ($email && !filter_var($email, FILTER_SANITIZE_EMAIL)) {

// CORRETO:
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
```

**Explicação:** `FILTER_VALIDATE_EMAIL` retorna `false` quando o e-mail é inválido. `FILTER_SANITIZE_EMAIL` apenas remove caracteres e retorna uma string, não valida.

---

## Bug 3 – JavaScript (modulos/pessoas/index.php)

**Sintoma:** Na página de Pessoas, a tabela não carrega, aparece "Erro ao carregar" ou requisições retornam 404.

**Causa:** Caminho da API incorreto. Falta um `../` no path.

**Local:** Constante `API` no `<script>` de `modulos/pessoas/index.php`

**Correção:** Ajustar o caminho da API:

```javascript
// ERRADO (bug):
const API = '../api/pessoas.php';

// CORRETO:
const API = '../../api/pessoas.php';
```

**Explicação:** A página está em `modulos/pessoas/index.php`. Para chegar em `api/pessoas.php` na raiz, é necessário subir dois níveis (`../../`).

---

## Bug 4 – CSS (modulos/agendas/index.php)

**Sintoma:** Ao abrir o modal de cadastro/edição de agenda, o modal aparece **atrás** do conteúdo da página (tabela, botões etc.), ficando inacessível.

**Causa:** `z-index` do overlay do modal muito baixo.

**Local:** Classe `.modal-overlay` no `<style>` de `modulos/agendas/index.php`

**Correção:** Aumentar o `z-index` do overlay:

```css
/* ERRADO (bug): */
.modal-overlay { ... z-index: 1; ... }

/* CORRETO: */
.modal-overlay { ... z-index: 1000; ... }
```

**Explicação:** O overlay precisa ficar acima do restante da página. Com `z-index: 1`, ele fica abaixo de elementos com `z-index` maior (ex.: tabela, nav). O valor `1000` garante que o modal fique por cima.

---

---

## Bug 5 – PHP (api/lembretes.php)

**Sintoma:** Ao excluir um lembrete, **todos os outros são removidos** e apenas o clicado permanece na lista.

**Causa:** Operador de comparação invertido no `array_filter` do método DELETE.

**Local:** Linha ~82 em `api/lembretes.php`

**Correção:** Trocar `===` por `!==` na condição do filtro:

```php
// ERRADO (bug):
$dados['lembretes'] = array_values(array_filter($dados['lembretes'], fn($l) => (int)$l['id'] === $id));

// CORRETO:
$dados['lembretes'] = array_values(array_filter($dados['lembretes'], fn($l) => (int)$l['id'] !== $id));
```

**Explicação:** O `array_filter` deve **manter** os registros cujo ID é diferente do que foi excluído (`!==`). Com `===`, faz o inverso: mantém apenas o excluído e descarta todos os outros.

---

## Bug 6 – JavaScript (modulos/lembretes/index.php)

**Sintoma:** Na listagem de Lembretes, a coluna "Data lembrete" exibe **"Invalid Date"** para todos os registros.

**Causa:** Remoção do `.replace(' ', 'T')` na função `formatarDataHora`. O construtor `new Date()` não aceita o formato `YYYY-MM-DD HH:MM:SS` com espaço em alguns browsers.

**Local:** Função `formatarDataHora` no `<script>` de `modulos/lembretes/index.php`

**Correção:** Restaurar o `.replace(' ', 'T')` antes de passar para `new Date()`:

```javascript
// ERRADO (bug):
const dt = new Date(d);

// CORRETO:
const dt = new Date(d.replace(' ', 'T'));
```

**Explicação:** As datas são salvas no formato `2026-04-25 08:00:00` (com espaço). O padrão ISO 8601 usa `T` como separador (`2026-04-25T08:00:00`). O `.replace(' ', 'T')` garante que `new Date()` interprete corretamente em todos os browsers.

---

## Bug 7 – CSS (modulos/tags/index.php)

**Sintoma:** No módulo de Tags, ao abrir o modal de "Nova tag" ou "Editar", o modal aparece **atrás** do conteúdo da página, ficando inacessível.

**Causa:** `z-index` do overlay do modal zerado.

**Local:** Classe `.modal-overlay` no `<style>` de `modulos/tags/index.php`

**Correção:** Restaurar o `z-index` para `1000`:

```css
/* ERRADO (bug): */
.modal-overlay { ... z-index: 0; ... }

/* CORRETO: */
.modal-overlay { ... z-index: 1000; ... }
```

**Explicação:** Com `z-index: 0`, o overlay fica na mesma camada de empilhamento dos demais elementos da página, sendo sobreposto pela tabela e outros conteúdos. O valor `1000` garante que o modal fique acima de tudo.

---

## Resumo

| # | Arquivo | Tipo | Correção |
|---|---------|------|----------|
| 1 | api/agendas.php | PHP | `>` → `<` na validação de data |
| 2 | api/pessoas.php | PHP | `FILTER_SANITIZE_EMAIL` → `FILTER_VALIDATE_EMAIL` (2 ocorrências) |
| 3 | modulos/pessoas/index.php | JS | `'../api/pessoas.php'` → `'../../api/pessoas.php'` |
| 4 | modulos/agendas/index.php | CSS | `z-index: 1` → `z-index: 1000` no `.modal-overlay` |
| 5 | api/lembretes.php | PHP | `=== $id` → `!== $id` no `array_filter` do DELETE |
| 6 | modulos/lembretes/index.php | JS | Restaurar `.replace(' ', 'T')` em `formatarDataHora` |
| 7 | modulos/tags/index.php | CSS | `z-index: 0` → `z-index: 1000` no `.modal-overlay` |
