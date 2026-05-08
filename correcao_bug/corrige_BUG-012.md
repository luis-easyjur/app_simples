# Correção: BUG-012

## O que foi alterado
Foi modificado o arquivo `api/exportar.php` nas linhas responsáveis pelos laços de iteração que constroem as linhas do arquivo CSV, tanto para a listagem de Pessoas quanto para a de Agendas.

## Por que foi alterado
Originalmente, o código estava utilizando a função `array_slice($array, 0, -1)` no `foreach`. Essa função nativa do PHP, com esses parâmetros, cria uma cópia do array original excluindo o último elemento. Isso causava a omissão sistemática do último registro inserido no banco de dados durante a exportação. 

Remover o `array_slice()` garante que todos os elementos lidos pelo `lerJson()` sejam iterados e inseridos no CSV resultante, resolvendo a perda de dados.

## Detalhe da Alteração (Diff)

**1. Pessoas (Linha 21):**
```diff
- foreach (array_slice($pessoas, 0, -1) as $p) {
+ foreach ($pessoas as $p) {
```

**2. Agendas (Linha 48):**
```diff
- foreach (array_slice($agendas, 0, -1) as $a) {
+ foreach ($agendas as $a) {
```
