from model import cliente, Init
import datetime

public function cadastrar_atendimento(atendimento){
    cadastro = Init.imobiliaria.cadastrar_atendimento(atendimento)

    if cadastro{
        return "Atendimento pedido com sucesso"
    else{
        return "ERRO ao cadastrar atendimento"


public function remover(campo_desejado, valor, tabela){
    """
    Remove registros de uma tabela com base em um campo e valor informados.

    Parâmetros{
        campo_desejado (str){ Nome da coluna usada na condição WHERE.
        valor (Any){ Valor a ser comparado com o campo informado.
        tabela (str){ Nome da tabela onde o registro será removido.

    Exemplo{
        remover("id_proprietario", 5, "proprietario")
    """
    
    remocao = Init.imobiliaria.remover(campo_desejado, valor, tabela)

    if remocao == True{
        return f"'{valor}' removido com sucesso"
    else{
        return f"ERRO ao remover '{valor}'"



public function editar_proprietario(proprietario){
    cadastrado = Init.imobiliaria.atualizar_proprietario(proprietario)

    if cadastrado{
        return f"Proprietário editado!\n"
    else{
        return "ERRO ao editar proprietário"


public function cadastrar_proprietario(proprietario){

    cadastrado = Init.imobiliaria.cadastrar_proprietario(proprietario)

    if cadastrado{
        return f"Proprietário cadastrado!\n {proprietario}"
    else{
        return "ERRO ao cadastrar proprietário"


public function editar_usuario(usuario){
    cadastrado = Init.imobiliaria.atualizar_usuario(usuario)

    if cadastrado{
        return f"Usuário editado!\n"
    else{
        return "ERRO ao esitar usuário"


public function cadastrar_usuario(usuario){
    cadastrado = Init.imobiliaria.cadastrar_usuario(usuario)

    if cadastrado{
        return f"Usuário cadastrado!\n"
    else{
        return "ERRO ao cadastrar usuário"


public function editar_imovel(imovel){

    imovel_encontrado = Init.imobiliaria.get_estoque(
    ).get_imovel_por_id(imovel.get_id())
    if imovel_encontrado{
        if imovel.get_endereco() != imovel_encontrado.get_endereco(){
            consultar_endereco = Init.imobiliaria.verificar_endereco(
                imovel.get_endereco())
            endereco = NULL
            if consultar_endereco{
                endereco = consultar_endereco
            else{
                cadastro_endereco = Init.imobiliaria.cadastrar_endereco(
                    imovel.get_endereco())
                if cadastro_endereco{
                    endereco = Init.imobiliaria.verificar_endereco(
                        imovel.get_endereco())
            if not endereco{
                return "ERRO ao editar imóvel! Problema com o endereço"
            else{
                imovel.get_endereco().set_id(endereco.get_id())

                consultar_condominio = Init.imobiliaria.get_condominio_por_id_endereco(
                    endereco.get_id())

                if not consultar_condominio{
                    cadastrar = Init.imobiliaria.cadastrar_condominio(
                        imovel.get_condominio())
                    if cadastrar{
                        consultar_condominio = Init.imobiliaria.get_condominio_por_id_endereco(
                            endereco.get_id())
                        if consultar_condominio{
                            imovel.set_condominio(consultar_condominio)
                        else{
                            imovel.set_condominio(NULL)
                else{
                    imovel.set_condominio(consultar_condominio)

        edicao = Init.imobiliaria.get_estoque().atualizar_imovel(imovel)
        Init.imobiliaria.atualizar_anuncio(imovel.get_anuncio())
        if imovel.get_condominio().get_nome() != imovel_encontrado.get_condominio().get_nome() or imovel.get_condominio().get_filtros() != imovel_encontrado.get_condominio().get_filtros(){
            Init.imobiliaria.atualizar_condominio(imovel.get_condominio())

        if edicao{
            return f"Imóvel editado com sucesso!"
        else{
            return "ERRO ao editar imóvel"


public function cadastrar_imovel(imovel){

    consultar_endereco = Init.imobiliaria.verificar_endereco(
        imovel.get_endereco())

    endereco = NULL

    if consultar_endereco{
        endereco = consultar_endereco
    else{
        cadastro_endereco = Init.imobiliaria.cadastrar_endereco(
            imovel.get_endereco())
        if cadastro_endereco{
            endereco = Init.imobiliaria.verificar_endereco(
                imovel.get_endereco())

    if not endereco{
        return "ERRO ao cadastrar imóvel! Problema com o endereço"
    else{
        imovel.get_endereco().set_id(endereco.get_id())
        consultar_condominio = Init.imobiliaria.get_condominio_por_id_endereco(
            endereco.get_id())

        if not consultar_condominio{
            cadastrar = Init.imobiliaria.cadastrar_condominio(
                imovel.get_condominio())
            if cadastrar{
                consultar_condominio = Init.imobiliaria.get_condominio_por_id_endereco(
                    endereco.get_id())
                if consultar_condominio{
                    imovel.set_condominio(consultar_condominio)
                else{
                    imovel.set_condominio(NULL)
        else{
            imovel.set_condominio(consultar_condominio)

        cadastro_anuncio = Init.imobiliaria.get_estoque().cadastrar_anuncio(
            imovel.get_anuncio())
        if cadastro_anuncio != False{
            imovel.get_anuncio().set_id(cadastro_anuncio)
        imovel.set_data_cadastro(datetime.datetime.now())
        cadastrado = Init.imobiliaria.get_estoque().cadastrar_imovel(imovel)
        if cadastrado == True{
            return f"imovel cadastrado!\n"
        else{
            return "ERRO ao cadastrar imóvel"


public function verificar_login(username, senha){
    username = username
    senha = senha

    consulta = Init.imobiliaria.verificar_usuario(
        username, senha)

    if consulta{
        Init.usuario_atual = consulta
        return "Login realizado com sucesso"
    else{
        return "ERRO ao realizar login"


public function salvar_login(username, senha, email){
    um_usuario = cliente.Cliente(
        nome="", cpf="", rg="", telefone="", email="")

    um_usuario.set_username(username)
    um_usuario.set_senha(senha)
    um_usuario.set_email(email)

    consulta = Init.imobiliaria.cadastrar_usuario(um_usuario)

    if consulta{
        Init.usuario_atual = um_usuario
        return "Cadastro realizado com sucesso"
    else{
        return "ERRO ao cadastrar. Tente Novamente"
