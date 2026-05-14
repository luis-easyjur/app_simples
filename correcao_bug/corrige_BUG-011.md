# Correção: BUG-011

## O que foi alterado
Foi modificado o arquivo `api/lembretes.php` para contemplar dois ajustes fundamentais em relação ao tratamento de datas: a configuração do fuso horário padrão (Timezone) e a padronização do formato de data no _fallback_ da requisição POST.

## Por que foi alterado
1. **Fuso Horário (Timezone):** Como o PHP estava utilizando o fuso horário padrão do servidor (geralmente UTC), as datas armazenadas no JSON (tanto em `data_lembrete` automático quanto no `criado_em`) estavam exibindo horas à frente do horário local brasileiro, afetando a cronologia correta. A configuração explícita do timezone resolveu essa distorção.
2. **Formato do Fallback:** Quando o cliente não enviava a data explicitamente no corpo da requisição, a API preenchia a `data_lembrete` com o formato brasileiro (`d/m/Y H:i:s`), destoando do ISO 8601 do resto do sistema e quebrando a lógica de ordenação e `strtotime()`. A alteração forçou o _fallback_ a utilizar o formato `Y-m-d H:i:s`.

## Detalhe da Alteração (Diff)

**1. Arquivo: `api/lembretes.php` (Linha 2) - Configuração do Timezone**
```diff
  <?php
+ date_default_timezone_set('America/Sao_Paulo');
  header('Content-Type: application/json; charset=utf-8');
```

**2. Arquivo: `api/lembretes.php` (Linha do Fallback) - Formato ISO**
```diff
- 'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s')),
+ 'data_lembrete' => trim($body['data_lembrete'] ?? date('Y-m-d H:i:s')),
```
