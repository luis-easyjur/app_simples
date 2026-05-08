# Plano de Correção: BUG-012

## 1. Contexto do Bug
O módulo de exportação (pessoas e agendas) está sempre omitindo o último registro do banco de dados na geração do arquivo CSV.

## 2. Causa Raiz
Analisando o arquivo responsável pela exportação (`api/exportar.php`), foi identificado o uso indevido da função `array_slice()` nos laços de repetição `foreach`.
Especificamente:
- `array_slice($pessoas, 0, -1)` (Linha 21)
- `array_slice($agendas, 0, -1)` (Linha 48)

O parâmetro `-1` no final do `array_slice` indica que a função deve retornar todo o array **exceto o último elemento**. Isso explica exatamente por que o último registro sempre fica de fora no arquivo CSV final.

## 3. Solução Proposta (Arquivos e Alterações)

### Arquivo: `api/exportar.php`

**Alteração 1: Exportação de Pessoas (Linha 21)**
- **De:** `foreach (array_slice($pessoas, 0, -1) as $p) {`
- **Para:** `foreach ($pessoas as $p) {`

**Alteração 2: Exportação de Agendas (Linha 48)**
- **De:** `foreach (array_slice($agendas, 0, -1) as $a) {`
- **Para:** `foreach ($agendas as $a) {`

## 4. Impacto Esperado (Comportamento Pós-Fix)
Removendo o `array_slice()`, o laço de repetição irá iterar sobre **todos** os elementos do array recuperado do banco de dados (`$pessoas` e `$agendas`), garantindo que o CSV gerado contenha 100% dos dados reais, sem omitir o último registro. O download continuará ocorrendo normalmente.

---
> Aguardando a aprovação do Desenvolvedor(a) para executar estas alterações.
