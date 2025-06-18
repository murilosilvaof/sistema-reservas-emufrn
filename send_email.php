<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $matricula = $_POST["matricula"];
    $email = $_POST["email"];
    $obs = $_POST["obs"];
    $sala = $_POST["sala"];
    $dia = $_POST["dia"];
    $horario = $_POST["horario"];
    $periodo = $_POST["periodo"];

    $instrumento = $_POST["instrumento"];
    $tipo_evento = $_POST["tipo_evento"];
    $perfil = $_POST["perfil"];

    $reservaId = uniqid();
    $destinatario = "";
    $assunto = "=?UTF-8?Q?" . quoted_printable_encode("NOVA SOLICITACAO DE RESERVA - $sala") . "?=";

     // 🖼️ Link direto da imagem da logo (subir para um servidor, Google Drive com link direto, ou use temporariamente um ngrok/public)
    $logo_url = "https://www.seudominio.com/assets/logo-marcato-emufrn.png";

    $mensagem = "
    <html>
    <head>
      <meta charset='UTF-8'>
      <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; color: #333; }
        .container {
          background: #ffffff; padding: 30px; border-radius: 10px; max-width: 600px;
          margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { max-width: 100%; height: auto; }
        h2 { color: #1596a8; text-align: center; }
        p { font-size: 14px; margin: 6px 0; }
        .botoes { text-align: center; margin-top: 30px; }
        .botoes a {
          display: inline-block; padding: 12px 20px; color: white;
          text-decoration: none; border-radius: 6px; font-weight: bold; margin: 5px;
        }
        .confirmar { background-color: #2ecc71; }
        .negar { background-color: #e74c3c; }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='logo'>
          <img src='$logo_url' alt='Logo Marcato EMUFRN' />
        </div>
        <h1>NOVA SOLICITAÇÃO DE RESERVA</h1>
        <p><strong>Nome:</strong> $nome</p>
        <p><strong>Instrumento:</strong> $instrumento</p>
        <p><strong>Matrícula:</strong> $matricula</p>
        <p><strong>Email:</strong> <a href='mailto:$email'>$email</a></p>
        <p><strong>Espaço:</strong> $sala</p>
        <p><strong>Tipo de Evento:</strong> $tipo_evento</p>
        <p><strong>Perfil:</strong> $perfil</p>
        <p><strong>Dia:</strong> $dia</p>
        <p><strong>Horário:</strong> $horario ($periodo)</p>
        <p><strong>Observações:</strong> $obs</p>
        <div class='botoes'>
          <a href='https://ismcursos.com/marcato/resposta.php?id=$reservaId&acao=confirmar' class='confirmar'>✔ Confirmar Reserva</a>
          <a href='https://ismcursos.com/marcato/resposta.php?id=$reservaId&acao=recusar' class='negar'>✖ Recusar Reserva</a>
        </div>
      </div>
    </body>
    </html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: producao@musica.ufrn.br\r\n";

    if (mail($destinatario, $assunto, $mensagem, $headers)) {
        echo "✅ Reserva enviada com sucesso! Verifique seu email.";
    } else {
        echo "❌ Erro ao enviar o email.";
    }
}
?>
