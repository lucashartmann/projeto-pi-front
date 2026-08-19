## Ideias:

- Botar um botao X na imagem no cadastro do imóvel para remover a imagem especifica
- Adicionar icons no cadastro
- Mudar fonte 

# Implementar:

## Bugs:

- [ ] Quando eu cadastro uma imagem ao imovel, ele remove as imagens de todos os imóveis pré cadastrados pelo **init** na tabela midia_anuncio
- [ ] Quando eu clico em semana ou dia no calendario, deixa de ser possivel adicionar um evento a uma data, pois o JS aplica só ao carregar a pagina
- [ ] Tem algum problema no cadastro de filtros no __init__, ele está cadastrando varios filtos em uma só row, e filtros duplicados.

## Geral:

- [ ] As notificaçoes botar q se um cliente for cadastrado mandar uma notificação para todos os corretores cadastrando por id do corretor na tabela notificacao e botar o tipo ser "atendimento', "cadastro", etc
- [ ] Adicionar clicks aos imóveis do banco, e também reiniciar o banco
- [ ] Adicionar quantidade de suites ao imovel no banco, controller, cadastro, dados-imovel e etc
- [ ] Refazer a proprietarioImovelDAO

### Login:

- [ ] Esqueceu senha: mandar o email

### Cadastro de imóvel:

- [ ] quantidade de visitas/clicks no imóvel
- Administrador
  - [ ] Administrador poder cadastrar uma logo, que vai aparecer sobre as imagens. E também .
- Admin, Corretor, Captador
  - [ ] Poder editar a posição e tamanho da logo, e poder girar a imagem ou até redimensionar/cortar talvez
- [ ] Abrir multiplas pessoas, apagar, midias e etc

### Index e Anuncios:

- [ ] Mostrar Imóveis mais acessados ou populares. Ordenar os outros por data de cadastro
- [ ] adicionar whats no anuncio

### Dados do imóvel:

- [ ] Melhorar tempo de carregamento de dados na dados-imovel

### Estoque e Anuncios:

- [ ] Salvar na sessao a tabela escolhida do select. Se for imóvel ou pessoas e qual tipo de pessoa
- [ ] Na tabela de pessoas botar um botao de editar ao lado do de apagar, e linkar o de apagar com a função de apagar

### Dados da imobiliaria:

- [ ] Melhorar design
- [ ] Implementar graficos

### Cadastro de contrato:

- [ ] Implementar

### Dados do cliente:

- [ ] Na tela de dados do cliente, se for outro tipo q n seja cliente e admin, n mostrar o botão de ver favoritos e ver atendimentos

### Atendimentos e Agendar visitas:

- [ ] Poder editar o atendimento e a visita, e talvez adicionar uma opção de cancelar a visita e apagar o atendimento. Mostrar mais detalhes
- [ ] Arrumar SQL de atendimento
