from model import cliente, imovel, captador, corretor, atendimento, endereco, anuncio, venda_aluguel, condominio, gerente, usuario, proprietario, imobiliaria


class Init{

    imobiliaria = imobiliaria.Imobiliaria("GameStart", "00000000000")
    usuario_atual = ""

    filtros_imovel = ["Aceita Pet", "Churrasqueira", "Armarios Embutidos", "Cozinha Americana", "Área de Serviço", "Suíte Master",
                      "Banheiro com Janela", "Piscina", "Lareira", "Ar Condicionado", "Semi Mobiliado", "Mobiliado", "Dependência de Empregada", "Despensa", "Depósito"]

    filtros_condominio = ["Churrasqueira Coletiva", "Piscina", "Piscina Infantil", "Piscina Aquecida", "Quiosque", "Sauna", "Quadra de Esportes", "Jardim", "Salão de Festas", "Academia", "Sala de Jogos", "Playground", "Brinquedoteca", "Vaga Coberta",
                          "Estacionamento", "Vaga para Visitantes", "Mercado", "Mesa de Sinuca", "Mesa de Ping Pong", "Mesa de Pebolim", "Quadra de Tenis", "Quadra de Futebol", "Quadra de Basquete", "Quadra de Volei", "Quadra de Areia", "Bicicletario", "Heliponto", "Elevador de Serviço"]

    imobiliaria.cadastrar_lista_filtros(filtros_imovel, "filtros_imovel")

    imobiliaria.cadastrar_lista_filtros(
        filtros_condominio, "filtros_condominio")

    administrador = usuario.Usuario(username="administrador", senha="123", email="admin@example.com",
                                    nome="Lucas", cpf_cnpj="00000000000", tipo=usuario.Tipo.ADMINISTRADOR)
    administrador_dois = usuario.Usuario(username="admin2", senha="123", email="admin2@example.com",
                                         nome="Felipe", cpf_cnpj="11111111111", tipo=usuario.Tipo.ADMINISTRADOR)
    gerente_um = gerente.Gerente(username="gerente", senha="123",
                                 email="gerente@example.com", nome="Pedro", cpf_cnpj="22222222222")
    gerente_dois = gerente.Gerente(username="gerente2", senha="123",
                                   email="gerente2@example.com", nome="Rosangela", cpf_cnpj="33333333333")
    comprador = cliente.Cliente(username="cliente", senha="123", email="cliente@example.com",
                                nome="Marcela", cpf_cnpj="44444444444")
    comprador_dois = cliente.Cliente(username="cliente2", senha="123", email="cliente2@example.com",
                                     nome="Rute Dois", cpf_cnpj="77777777777")
    captador_um = captador.Captador(username="captador", senha="123", email="captador@example.com",
                                    nome="Ana", cpf_cnpj="55555555555")
    captador_dois = captador.Captador(username="captador2", senha="123", email="captador2@example.com",
                                      nome="Ana Dois", cpf_cnpj="88888888888")
    corretor_um = corretor.Corretor(username="corretor", senha="123", email="corretor@example.com",
                                    nome="João", cpf_cnpj="66666666666", creci="123456")
    corretor_dois = corretor.Corretor(username="corretor2", senha="123", email="corretor2@example.com",
                                      nome="Elisabeth", cpf_cnpj="99999999999", creci="654321")

    if not imobiliaria.get_lista_usuarios(){

        imobiliaria.cadastrar_usuario(administrador)
        imobiliaria.cadastrar_usuario(gerente_um)
        imobiliaria.cadastrar_usuario(comprador)
        imobiliaria.cadastrar_usuario(captador_um)
        imobiliaria.cadastrar_usuario(corretor_um)
        imobiliaria.cadastrar_usuario(administrador_dois)
        imobiliaria.cadastrar_usuario(gerente_dois)
        imobiliaria.cadastrar_usuario(comprador_dois)
        imobiliaria.cadastrar_usuario(captador_dois)
        imobiliaria.cadastrar_usuario(corretor_dois)

    proprietario_um = proprietario.Proprietario(email="proprietario@example.com",
                                                nome="Maria", cpf_cnpj="00000000000")

    proprietario_dois = proprietario.Proprietario(email="proprietario2@example.com",
                                                  nome="Joaquim", cpf_cnpj="11111111111")

    if not imobiliaria.get_lista_proprietarios(){

        imobiliaria.cadastrar_proprietario(proprietario_dois)
        imobiliaria.cadastrar_proprietario(proprietario_um)

    endereco_um = endereco.Endereco(
        rua="Rua A", bairro="Centro", cep=12345678, cidade="Cidade X", uf="Estado Y")
    endereco_um.set_numero("123")

    endereco_dois = endereco.Endereco(
        rua="Rua B", bairro="Bairro Z", cep=87654321, cidade="Cidade W", uf="Estado V")
    endereco_dois.set_numero("456")

    if not imobiliaria.get_lista_enderecos(){
        cadastro = imobiliaria.cadastrar_endereco(endereco_um)
        cadastro_dois = imobiliaria.cadastrar_endereco(endereco_dois)
    consulta_um = NULL
    consulta_dois = NULL
    consulta_um = imobiliaria.verificar_endereco(endereco_um)
    condominio_um = condominio.Condominio("Way", consulta_um)
    imobiliaria.cadastrar_condominio(condominio_um)
    consultar_condominio = NULL
    if consulta_um{
        consultar_condominio = imobiliaria.get_condominio_por_id_endereco(
            consulta_um.get_id())
    anuncio_um = anuncio.Anuncio()

    with open(r"assets\apartamento2\5661211031.jpg", "rb") as f{
        blob = f.read()
        
    with open(r"assets\apartamento2\5661211294.jpg", "rb") as f{
        blob2 = f.read()

    anuncio_um.set_imagens([blob, blob, blob2, blob2, blob])
    anuncio_um.set_titulo("Apartamento de 1 quarto, venda ou aluguel")
    anuncio_um.set_descricao("Imóvel com uma posição privilegiada, próximo a parques, shoppings e com fácil acesso ao transporte público. O apartamento possui uma sala aconchegante, cozinha funcional, banheiro moderno e um quarto confortável. Ideal para quem busca praticidade e qualidade de vida.")
    
    anuncio_dois = anuncio.Anuncio()
    
    with open(r"assets\apartamento1\5661162882.jpg", "rb") as f{
        blob = f.read()
        
    anuncio_dois.set_imagens([blob, blob, blob, blob, blob])
    anuncio_dois.set_titulo("Apartamento de 2 quartos, venda ou aluguel")
    anuncio_dois.set_descricao("Imóvel localizado no centro da cidade, próximo a escolas, supermercados e com fácil acesso ao transporte público. O apartamento possui uma sala ampla, cozinha americana, banheiro social e um quarto espaçoso. Ideal para quem busca conforto e praticidade.")

    imovel_um = imovel.Imovel(
        endereco=consulta_um, status=imovel.Status.VENDA_ALUGUEL, categoria=imovel.Categoria.APARTAMENTO)
    imovel_um.set_valor_aluguel(1500)
    imovel_um.set_valor_venda(300000)
    imovel_dois = imovel.Imovel(
        endereco=consulta_um, status=imovel.Status.ALUGUEL, categoria=imovel.Categoria.APARTAMENTO)
    imovel_dois.set_valor_aluguel(2000)
    imovel_tres = imovel.Imovel(
        endereco=consulta_um, status=imovel.Status.VENDIDO, categoria=imovel.Categoria.LOFT)
    
    if not imobiliaria.get_estoque().get_lista_imoveis(){
        cadastro_anuncio = imobiliaria.get_estoque().cadastrar_anuncio(anuncio_um)
        cadastro_anuncio2 = imobiliaria.get_estoque().cadastrar_anuncio(anuncio_dois)
        if cadastro_anuncio is not NULL{
            anuncio_um.set_id(cadastro_anuncio)
            imovel_um.set_anuncio(anuncio_um)
            if consultar_condominio{
                imovel_um.set_condominio(consultar_condominio)
            imobiliaria.get_estoque().cadastrar_imovel(imovel_um)

        if cadastro_anuncio2 is not NULL{
            anuncio_dois.set_id(cadastro_anuncio2)
            imovel_dois.set_anuncio(anuncio_dois)
            imovel_dois.set_condominio(consultar_condominio)
            imobiliaria.get_estoque().cadastrar_imovel(imovel_dois)

        cadastro_anuncio = imobiliaria.get_estoque().cadastrar_anuncio(
            anuncio_um)

        if cadastro_anuncio is not NULL{
            anuncio_um.set_id(cadastro_anuncio)
            imovel_tres.set_anuncio(anuncio_um)
            imovel_tres.set_condominio(consultar_condominio)
            imobiliaria.get_estoque().cadastrar_imovel(imovel_tres)

    consulta_dois = imobiliaria.verificar_endereco(endereco_dois)
    condominio_dois = condominio.Condominio("Premium", consulta_dois)
    imobiliaria.cadastrar_condominio(condominio_dois)
    condominio_dois = NULL
    if consulta_dois{
        condominio_dois = imobiliaria.get_condominio_por_id_endereco(
            consulta_dois.get_id())
    imovel_quatro = imovel.Imovel(
        endereco=consulta_dois, status=imovel.Status.PENDENTE, categoria=imovel.Categoria.TERRENO)
    imovel_cinco = imovel.Imovel(
        endereco=consulta_dois, status=imovel.Status.VENDA_ALUGUEL, categoria=imovel.Categoria.CASA)
    if not imobiliaria.get_estoque().get_lista_imoveis(){
        cadastro_anuncio = imobiliaria.get_estoque().cadastrar_anuncio(
            anuncio_um)

        if cadastro_anuncio is not NULL{
            anuncio_um.set_id(cadastro_anuncio)
            imovel_quatro.set_anuncio(anuncio_um)
            imovel_quatro.set_condominio(condominio_dois)
            imobiliaria.get_estoque().cadastrar_imovel(imovel_quatro)

        cadastro_anuncio = imobiliaria.get_estoque().cadastrar_anuncio(
            anuncio_um)

        if cadastro_anuncio is not NULL{
            anuncio_um.set_id(cadastro_anuncio)
            imovel_cinco.set_anuncio(anuncio_um)
            imovel_cinco.set_condominio(condominio_dois)
            imobiliaria.get_estoque().cadastrar_imovel(imovel_cinco)
            
    atendimento_um = atendimento.Atendimento()
    atendimento_um.set_cliente(comprador)
    atendimento_um.set_corretor(corretor_um)
    atendimento_um.set_imovel(imovel_um)
    atendimento_um.set_status(atendimento.Status.EM_ANDAMENTO)
    atendimento_dois = atendimento.Atendimento()
    atendimento_dois.set_cliente(comprador_dois)
    atendimento_dois.set_corretor(corretor_dois)
    atendimento_dois.set_imovel(imovel_dois)
    atendimento_dois.set_status(atendimento.Status.PENDENTE)
    if not imobiliaria.get_lista_atendimentos(){
        imobiliaria.cadastrar_atendimento(atendimento_um)
        imobiliaria.cadastrar_atendimento(atendimento_dois)
