# Plano de Correção: BUG-011

## 1. Contexto do Bug
Quando um lembrete é criado sem que a data (`data_lembrete`) seja informada explicitamente, o sistema está preenchendo automaticamente com o formato brasileiro (`DD/MM/AAAA HH:MM:SS`), causando inconsistência no JSON e problemas na ordenação temporal (já que o sistema espera o padrão ISO `YYYY-MM-DD HH:MM:SS`).

## 2. Causa Raiz
Analisando o arquivo da API de lembretes (`api/lembretes.php`), no bloco responsável pelo método `POST` (criação de um novo lembrete), existe um _fallback_ (valor padrão) sendo atribuído caso `data_lembrete` venha vazio:

- Linha do Fallback: `'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s'))`

A função `date('d/m/Y H:i:s')` está injetando a string no formato dia/mês/ano em vez de ano-mês-dia.

## 3. Solução Proposta (Arquivos e Alterações)

### Arquivo: `api/lembretes.php`

**Alteração 1: Padronização da Data Default**
- **De:** `'data_lembrete' => trim($body['data_lembrete'] ?? date('d/m/Y H:i:s')),`
- **Para:** `'data_lembrete' => trim($body['data_lembrete'] ?? date('Y-m-d H:i:s')),`

## 4. Impacto Esperado (Comportamento Pós-Fix)
Com essa modificação, qualquer novo lembrete gerado sem uma data explícita passará a adotar o formato universal ISO 8601 (`AAAA-MM-DD HH:MM:SS`). Isso garante consistência com os demais dados do banco e permite que algoritmos de ordenação e processamento de tempo (`strtotime`) funcionem perfeitamente.

---
> Aprovado e revertido para o escopo estrito da task.
