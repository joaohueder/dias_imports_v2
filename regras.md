### REGRAS E ORIENTAÇÕES PARA A CRIAÇÃO E MANUTENÇÃO DO PROJETO

## TECNOLOGIAS ENVOLVIDAS

- CodeIgniter 4
- Bootstrap 5
- JavaScript
- jQuery
- AJAX
- SCSS
- TABLER
- MySQL

## REGRAS DO PROJETO
- Nunca altere ou crie algo dentro do core do framework do codeigniter
- O projeto deve ser documentado "doc/" granularmente para que o claude code não demore e nem consulma muitos tokens analisando todo o projeto, monte os arquivos com um index capaz de direcionar o claude code para economia e velocidade.
- Se houver algum SQL para executar no banco, avise no final do chat em destaque colorido.
- Leia o arquivo "/doc/index.md" para entender em qual doc está a documentação da solicitação atual, utilize para não sair do padrão e nem gastar tokens tendo que ler todo o projeto.


## BANCO DE DADOS
- Crie e atualize sempre o arquivo "bd/master.sql" arquivo esse que será capaz de reconstruir ou instalar o banco de dados por completo.
- Sempre crie o arquivo sequencial "bd/[sequencia]-titulo.sql" e a respectiva Migration no CodeIgniter (`sistema/app/Database/Migrations/`) para qualquer alteração/construção/melhoria no sistema.
- **SEMPRE execute a migração imediatamente no banco de dados** (via `php sistema/spark migrate` ou execução direta do SQL) em toda alteração, garantindo que o banco esteja sempre atualizado e compatível, sem perda de dados existentes.

## LAYOUT
- O botão Salvar e cancelar no desktop deve ser flutuante e só aparecer quando tiver algo para ser salvo.
- Ao executar alguma ação critica como excluir, inativar, etc, deve mostrar um modal com um icone divertido e animado, uma mensagem amigavel e explicativa e botões de confirmação.


## CAMPOS E COMPONENTES
- WhatsApp e Telefone obrigatoriamente deve ter mascaras na digitação