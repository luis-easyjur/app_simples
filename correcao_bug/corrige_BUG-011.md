# Correção: BUG-011

## O que foi alterado
Foi modificado o arquivo `api/lembretes.php` na linha responsável pelo _fallback_ (valor padrão) da criação de um novo registro de lembrete.

## Por que foi alterado
Quando o cliente não enviava a data explicitamente no corpo da requisição, a API estava preenchendo automaticamente a `data_lembrete` com o formato brasileiro (`d/m/Y H:i:s`). Como o padrão do sistema (e de outras datas do próprio lembrete, como `criado_em`) é o ISO 8601, essa divergência estava quebrando a ordenação cronológica e impedindo o uso correto de funções como `strtotime()`. 

A alteração forçou o _fallback_ a utilizar o formato `Y-m-d H:i:s`, garantindo 100% de consistência.

## Detalhe da Alteração (Diff)

**Arquivo: `api/lembretes.php` (Linha do Fallback)**
```diff
- 'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s')),
+ 'data_lembrete' => trim($body['data_lembrete'] ?? date('Y-m-d H:i:s')),
```
