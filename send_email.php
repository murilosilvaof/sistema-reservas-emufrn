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

    // Gera um ID único para essa reserva
    $reservaId = uniqid();

    // Seu email (responsável pela confirmação)
    $destinatario = "murilo.silva.136@ufrn.edu.br";

    $assunto = "Solicitação de reserva - $sala";

    $mensagem = "
        <html>
        <head>
            <style>
                a.botao {
                    display: inline-block;
                    padding: 10px 20px;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .confirmar { background-color: #4CAF50; }
                .negar { background-color: #f44336; }
            </style>
        </head>
        <body>
            <h2>Nova solicitação de reserva</h2>
            <p><strong>Nome:</strong> $nome</p>
            <p><strong>Matrícula:</strong> $matricula</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Data:</strong> $dia</p>
            <p><strong>Horário:</strong> $horario ($periodo)</p>
            <p><strong>Sala:</strong> $sala</p>
            <p><strong>Observações:</strong> $obs</p>
            <br><br>
            <a href='https://seudominio.com/resposta.php?id=$reservaId&status=confirmado' class='botao confirmar'>✅ Confirmar Reserva</a>
            &nbsp;
            <a href='https://seudominio.com/resposta.php?id=$reservaId&status=negado' class='botao negar'>❌ Negar Reserva</a>
        </body>
        </html>
    ";

    // Cabeçalhos do email
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: reservas@emufrn.com.br\r\n";

    // Envia o email
    if (mail($destinatario, $assunto, $mensagem, $headers)) {
        echo "✅ Reserva enviada com sucesso! Verifique seu email.";
    } else {
        echo "❌ Erro ao enviar o email. Verifique o servidor.";
    }
}
?>
