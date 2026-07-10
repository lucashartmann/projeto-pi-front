<?php
function getArquivo()
{
  return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
body {
    margin: 0;
    font-family: Verdana, Arial, sans-serif;
    font-size: 12px;
    color: #666666;
}
a {
    color: #666666;
    text-decoration: none;
}
a:hover {
    color: #FF0000;
}
</style>
</head>
<body>
    <h1>Recuperação de Senha</h1>

    <p>Olá,</p>

    <p>Recebemos uma solicitação para recuperar a senha da sua conta. Se você fez essa solicitação, clique no link abaixo para criar uma nova senha:</p>

    <p>
        <a href="{{LINK_DE_RECUPERACAO}}">
            Recuperar Senha
        </a>
    </p>

    <p>Se você não solicitou a recuperação de senha, por favor ignore este e-mail.</p>

    <p>Atenciosamente,<br>Equipe de Suporte</p>
</body>
</html>
HTML;
}
