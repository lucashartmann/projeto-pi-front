## Ideias:

- <font color=yellow>[TALVEZ]</font> Adicionar icons em outros lugares
- <font color=yellow>[TALVEZ]</font> Dar sugestao no input ao digitar
- <font color=yellow>[TALVEZ]</font> Editar a logo em cada foto individual do imóvel
- <font color=yellow>[TALVEZ]</font> Dar destaque maior para os valores e mudar a ordem, se baseando nos comércios como Mercado Livre, OLX.
- <font color=green>[IMPROVÁVEL]</font> Adicionar icons no cadastro

## Bugs:

- [ ] `JS` <font color=yellow>[MÉDIO]</font> Quando eu clico em semana ou dia no calendario, deixa de ser possivel adicionar um evento a uma data, pois o JS aplica só ao carregar a pagina
- [ ] `PHP` <font color=yellow>[verificar]</font> <font color=green>[BAIXO]</font> Quando eu cadastro uma imagem ao imovel, ele remove as imagens de todos os imóveis pré cadastrados pelo **init** na tabela midia_anuncio
- [ ] `PHP` <font color=green>[BAIXO]</font>
      Tem algum problema no cadastro de filtros no **init**, ele está cadastrando varios filtos em uma só row, e filtros duplicados.

## Geral:

- [ ] `CSS` <font color=red>[ALTO]</font> Mudar fonte
- [ ] `CSS` <font color=red>[ALTO]</font> Responsividade
- [ ] `JS` <font color=red>[ALTO]</font> Botão para limpar os inputs, ou as imagens, documentos e etc
- [ ] `CSS` `JS` <font color=red>[ALTO]</font> Ver como fazer o menu de filtros responsivo
- [ ] `PHP` `HTML` `CSS` `JS` `SQL` <font color=red>[ALTO]</font> Contratos
- [ ] `PHP` <font color=green>[BAIXO]</font> Refazer a proprietarioImovelDAO
- [ ] `PHP` `JS` <font color=green>[BAIXO]</font> As notificaçoes botar q se um cliente for cadastrado mandar uma notificação para todos os corretores cadastrando por id do corretor na tabela notificacao e botar o tipo ser "atendimento', "cadastro", etc


### Login:

- [ ] `JS` `PHP` <font color=red>[ALTO]</font> Esqueceu senha: mandar o email

### Cadastro de imóvel:

- [ ] `JS` <font color=red>[ALTO]</font> Implementar swiper ao abrir imagem como é no dados-imovel.js
- [ ] `JS` <font color=yellow>[MÉDIO]</font> Abrir multiplas pessoas, apagar, midias e etc
- [ ] `JS` <font color=yellow>[MÉDIO]</font> Atualizar o mapa depois de ter cep e numero
- Administrador
  - [ ] `JS` `PHP` `SQL` <font color=green>[BAIXO]</font> 
  Poder Aumentar e diminuir a logo, salvar a posição e tamanho dela em relação á imagem, quanto carregar o JS, carregar a posiçao e a logo salva do banco de dados
- Admin, Corretor, Captador
  - [ ] `JS` `PHP` `SQL` <font color=green>[BAIXO]</font> Poder editar a posição e tamanho da logo, e poder girar a imagem ou até redimensionar/cortar talvez

### Index e Anuncios:

- [ ] `JS` <font color=green>[BAIXO]</font> Ordenar imoveis por data de cadastro
- [ ] `JS` `HTML` `CSS` <font color=green>[BAIXO]</font> adicionar whats no anuncio


### Dados da imobiliaria:

- [ ] `CSS` `HTML` <font color=red>[ALTO]</font> Melhorar design
- [ ] `JS` <font color=red>[ALTO]</font> Implementar graficos de faturamento, visitas, comparar com meses anteriores, anos, semestres e etc. Assim como a quantidade de imoveis e etc.

### Cadastro de contrato:

- [ ] `JS` `HTML` `CSS` <font color=red>[ALTO]</font> Implementar

### Dados do cliente:

- [ ] `JS` <font color=yellow>[MÉDIO]</font> Na tela de dados do cliente, se for outro tipo q n seja cliente e admin, n mostrar o botão de ver favoritos e ver atendimentos

### Atendimentos e Agendar visitas:

- [ ] `JS` `HTML` `CSS` `PHP` <font color=red>[ALTO]</font> Poder editar o atendimento e a visita, e talvez adicionar uma opção de cancelar a visita e apagar o atendimento. Mostrar mais detalhes
- [ ] `PHP` <font color=yellow>[MÉDIO]</font> Arrumar SQL de atendimento

### Agendar visita:

- [ ] `PHP` `JS` <font color=yellow>[MÉDIO]</font> Mandar email para cliente ou fazer alert caso ele n tenha o email cadastrado
- [ ] `JS` `HTML` `CSS` `PHP` <font color=yellow>[MÉDIO]</font> Testar
