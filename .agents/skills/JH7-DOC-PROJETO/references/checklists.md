# Checklists

## Criação do documento

Levantamento:

- [ ] `AGENTS.md` / `CLAUDE.md` lidos e suas regras refletidas no documento
- [ ] `package.json` lido — stack e versões reais, não presumidas
- [ ] Todas as migrações SQL lidas em ordem numérica
- [ ] Rotas e layout mapeados a partir do código, não do nome das pastas
- [ ] Contextos globais entendidos (auth, tema, configurações)
- [ ] Camada de dados (`lib/`) mapeada por módulo
- [ ] Edge Functions lidas e o motivo de cada uma identificado
- [ ] Tokens de estilo extraídos do CSS real

Documento:

- [ ] As 12 seções presentes, na ordem
- [ ] Diagrama Mermaid de arquitetura e `erDiagram` do banco
- [ ] Regras de negócio numeradas `RN-xx`
- [ ] Matriz de permissão por módulo e ação
- [ ] Índice de migrações completo
- [ ] Nenhum segredo escrito — só nome de variável e onde vive
- [ ] Nada inventado; o que não veio do código está `⚠️ A CONFIRMAR`
- [ ] Roadmap separado do implementado
- [ ] Changelog inicializado com a versão atual
- [ ] Perguntas abertas listadas na resposta ao usuário

---

## Atualização após alteração

- [ ] Documento lido antes de codificar
- [ ] Conflito entre pedido e documento levantado com o usuário
- [ ] Mapa de impacto de `fluxo-manutencao.md` aplicado
- [ ] Edição cirúrgica: só as seções afetadas
- [ ] Regra revogada marcada como descontinuada, não apagada
- [ ] Diagrama do banco atualizado se houve mudança de estrutura
- [ ] Matriz de permissão atualizada se houve módulo ou papel novo
- [ ] Índice de migrações atualizado se houve SQL novo
- [ ] Linha de changelog escrita
- [ ] Versão igual no documento e no rodapé da aplicação
- [ ] Nenhum segredo introduzido

---

## Auditoria

- [ ] Segurança e permissões conferidas contra RLS e rotas
- [ ] Cada tabela do documento existe no banco com as colunas descritas
- [ ] Cada tabela do banco existe no documento
- [ ] Módulos e rotas do documento existem no código
- [ ] Variáveis de ambiente do documento existem no projeto
- [ ] Integrações e versões de API conferidas
- [ ] Tokens de estilo conferidos contra o CSS
- [ ] Divergências reportadas com gravidade
- [ ] Onde o código divergiu de decisão de produto, reportado ao usuário em vez de silenciado no documento

---

## Bloqueios que exigem parar e perguntar

- Regra de negócio que não dá para deduzir do código.
- Documento e código em contradição direta, sem saber qual é a intenção.
- Pedido que exige mudar uma regra já documentada.
- Alteração que apagaria ou modificaria dados existentes.
