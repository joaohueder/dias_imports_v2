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
- Sempre crie o arquivo sequencial "bd/[sequencia]-titulo" para executar após uma alteração/construção/melhoria no sistema.
- Sempre execute o sql no banco mas nunca perca dados já registrado no banco, faça um migração sem perder dados.

## LAYOUT
- O botão Salvar e cancelar no desktop deve ser flutuante e só aparecer quando tiver algo para ser salvo.
- Ao executar alguma ação critica como excluir, inativar, etc, deve mostrar um modal com um icone divertido e animado, uma mensagem amigavel e explicativa e botões de confirmação.


## CAMPOS E COMPONENTES
- WhatsApp e Telefone obrigatoriamente deve ter mascaras na digitação