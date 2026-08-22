# Estrutura obrigatória do `docs/PROJETO.md`

Ordem fixa. Nenhuma seção pode ser omitida — se algo não se aplica ao projeto, escreva "Não se aplica" e o motivo em uma linha.

---

## 0. Cabeçalho

Nome do sistema, uma frase que explica o que ele faz, versão atual (`ano.mes.sequencia`), data da última atualização e o aviso de que este é o documento mestre.

---

## 1. Visão de negócio

Por que o sistema existe. Sem isso, decisões técnicas ficam sem critério.

- Problema que resolve, em linguagem de quem paga a conta.
- Quem usa: cada perfil, o que faz no sistema e o que espera dele.
- Jornada principal, do primeiro acesso ao resultado de valor.
- Regras de negócio, numeradas (`RN-01`, `RN-02`...) para poderem ser citadas em código e conversa. Cada regra: enunciado, onde é aplicada no código, e o que acontece quando é violada.
- O que o sistema deliberadamente **não** faz. Evita pedido fora de escopo virar código.
- Métricas que importam ao negócio.

---

## 2. Arquitetura

- Stack com versões reais lidas do `package.json`.
- Diagrama Mermaid do fluxo: cliente → camada de dados → serviços externos.
- Estrutura de pastas comentada, só nos diretórios que importam.
- Onde cada tipo de código roda: browser, Edge Function, banco. E o critério que decide isso (normalmente: se envolve segredo, roda no servidor).
- Variáveis de ambiente: nome, onde vive, para que serve, se é pública. **Nunca o valor.**
- Como buildar, rodar e publicar.

---

## 3. Banco de dados

A seção mais consultada. Precisa bater exatamente com as migrações.

- Diagrama Mermaid `erDiagram` das tabelas e relações.
- Uma subseção por tabela: propósito, colunas (nome · tipo · nulo · default · significado), chaves, índices, constraints e triggers.
- Views e o que elas escondem de propósito (ex: view pública que nunca expõe credencial).
- Funções e procedures: assinatura, se é `security definer`, para que serve, quem pode executar.
- Políticas RLS por tabela: operação, papel, condição, e a intenção em uma linha.
- Storage: buckets, se são públicos, quem lê e quem escreve.
- Realtime: tabelas publicadas.
- Convenções em vigor (nomes em snake_case, `created_at`/`updated_at`, uuid como PK, etc.).
- Índice das migrações: arquivo · o que introduziu.
- Regra de migração do projeto: aditiva, nunca destrutiva, nunca apaga ou altera registro existente.

---

## 4. Layout e experiência

- Tokens: paleta (claro e escuro), tipografia, espaçamento, raio, sombra, transição — com o nome real da variável CSS.
- Estrutura visual: shell, navegação, área de conteúdo, largura máxima, responsividade.
- Padrões de componente: modal, formulário, tabela, card, badge, toggle de tema.
- Estados obrigatórios de cada tela: carregando, vazio, erro, sucesso, sem permissão.
- Comportamentos exigidos pelo projeto (ler de `AGENTS.md`): atualização em tempo real sem refresh, tela de espera bloqueante durante ação de banco/API, tela amigável quando não há registro, modal de confirmação em ação crítica, versão no rodapé.
- Acessibilidade: contraste, foco visível, navegação por teclado, rótulo em controle sem texto.
- Idioma e formatos: moeda, data, telefone.

---

## 5. Segurança e permissões

- Fluxo de autenticação: onde a sessão nasce, onde é guardada, como expira.
- Autorização: papéis, matriz de permissão por módulo e ação (`view`, `create`, `edit`, `delete`).
- Como a rota é protegida no front e como o dado é protegido no banco. Deixe explícito que o front esconde e o banco impede — proteção real é no banco.
- Módulos sem permissão ficam ocultos na interface.
- Onde vivem os segredos e por que a chamada externa passa por proxy no servidor.
- Validação de entrada nos limites do sistema.
- Uploads: tipos aceitos, tamanho, onde são gravados.
- Riscos conhecidos e mitigação.

---

## 6. Módulos

Uma subseção por módulo, no mesmo formato:

- Rota e arquivos.
- O que o usuário faz ali.
- Tabelas usadas.
- Permissão exigida.
- Regras de negócio aplicadas (cite `RN-xx`).
- Integrações acionadas.
- Estados e validações da tela.

---

## 7. Integrações externas

Por serviço: para que serve, versão da API, onde as credenciais vivem, endpoints usados, caminho da chamada (quem chama de onde), tratamento de erro e limite de uso.

---

## 8. Convenções de código

Nomenclatura, organização de arquivo, onde fica acesso a dados, tratamento de erro, tipagem, estilo de comentário, política de commit. Só o que o projeto realmente pratica.

---

## 9. Roadmap

O que ainda não existe. Separado do resto para não ser confundido com implementado.

---

## 10. Changelog

Tabela `Versão · Data · O que mudou · Arquivos e migrações`, mais recente no topo. Toda alteração no sistema gera uma linha aqui.

---

## 11. Pendências

Tudo marcado `⚠️ A CONFIRMAR` no documento, consolidado como lista de perguntas ao dono do projeto.
