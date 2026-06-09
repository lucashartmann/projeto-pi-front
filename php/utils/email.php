<?php



function getArquivo(){
    $arquivo = "
  <style type='text/css'>
  body {
  margin:0px;
  font-family:Verdane;
  font-size:12px;
  color: #666666;
  }
  a{
  color: #666666;
  text-decoration: none;
  }
  a:hover {
  color: #FF0000;
  text-decoration: none;
  }
  </style>
    <html>
    <body>
        <h1>Recuperação de Senha</h1>
        <p>Olá,</p>
        <p>Recebemos uma solicitação para recuperar a senha da sua conta. Se você fez essa solicitação, clique no link abaixo para criar uma nova senha:</p>
        <p><a href='{{LINK_DE_RECUPERACAO}}'>Recuperar Senha</a></p>
        <p>Se você não solicitou a recuperação de senha, por favor ignore este email.</p>
        <p>Atenciosamente,<br>Equipe de Suporte</p>
    </body>
    </html>
  ";
    return $arquivo;
}