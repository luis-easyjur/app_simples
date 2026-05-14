# Plano de Correção: BUG-011

## 1. Contexto do Bug
Quando um lembrete é criado sem que a data (`data_lembrete`) seja informada explicitamente, ocorrem dois problemas de formatação de data:
1. O sistema preenche automaticamente com o formato brasileiro (`DD/MM/AAAA HH:MM:SS`), causando inconsistência no JSON e problemas na ordenação temporal (já que o sistema espera o padrão ISO `AAAA-MM-DD HH:MM:SS`).
2. O horário salvo no campo `criado_em` e no fallback de `data_lembrete` registra uma diferença de fuso horário em relação ao horário local, avançando as horas (e até o dia) incorretamente.

## 2. Causa Raiz
Analisando o arquivo da API de lembretes (`api/lembretes.php`), foram identificados dois pontos:
1. No método `POST`, existe um _fallback_ sendo atribuído caso `data_lembrete` venha vazio: `'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s'))`. A função injeta a string num formato não-ISO.
2. O PHP está rodando sob a configuração de fuso horário padrão do servidor (geralmente UTC ou Europa), o que faz com que as funções `date()` retornem o horário incorreto para a região de Brasília (America/Sao_Paulo).

## 3. Solução Proposta (Arquivos e Alterações)

### Arquivo: `api/lembretes.php`

**Alteração 1: Injeção do Fuso Horário Local (Linha 2)**
- Inserir a instrução `date_default_timezone_set('America/Sao_Paulo');` no topo do arquivo.

**Alteração 2: Padronização da Data Default (Linha 48 original)**
- **De:** `'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s')),`
- **Para:** `'data_lembrete' => trim($body['data_lembrete'] ?? date('Y-m-d H:i:s')),`

## 4. Impacto Esperado (Comportamento Pós-Fix)
As datas criadas sem envio explícito adotarão o formato ISO 8601 (`AAAA-MM-DD HH:MM:SS`), garantindo consistência. Além disso, todas as datas criadas no arquivo (incluindo o `criado_em`) respeitarão o fuso horário oficial do Brasil, gravando horas precisas no JSON.

---
> Execução Aprovada. Atualizado pós-descoberta de timezone.
