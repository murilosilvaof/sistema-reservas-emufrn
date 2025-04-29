<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Dados do formulário
$nome = $_POST['nome'] ?? '';
$instrumento = $_POST['instrumento'] ?? '';
$matricula = $_POST['matricula'] ?? '';
$email = $_POST['email'] ?? '';
$sala = $_POST['sala'] ?? '';
$tipo_evento = $_POST['tipo_evento'] ?? '';
$perfil = $_POST['perfil'] ?? '';
$dia = $_POST['dia'] ?? '';
$horario = $_POST['horario'] ?? '';
$obs = $_POST['obs'] ?? '';

// Conexão com banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservas_emufrn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO reservas_espacos (nome, instrumento, matricula, email, sala, tipo_evento, perfil, dia, horario, obs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $nome, $instrumento, $matricula, $email, $sala, $tipo_evento, $perfil, $dia, $horario, $obs);
$salvo_no_banco = $stmt->execute();

// 💥 Aqui já salva o ID da nova reserva antes de fechar
$id_nova_reserva = $conn->insert_id;

$stmt->close();
$conn->close();

// Envio do e-mail
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'murilodevweb@gmail.com'; // Seu e-mail
    $mail->Password   = 'idzt npdn cfgo nyau';    // Sua senha de app
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remetente e destinatário
    $mail->setFrom('murilodevweb@gmail.com', 'Sistema de Reservas EMUFRN');
    $mail->addAddress('murilodevweb@gmail.com', 'Murilo Silva');

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = "Nova Solicitação de Reserva - $sala";
    
    $link_confirmar = "http://localhost/projeto-reserva-salas/reservas_teste_email/reposta.php?id={$id_nova_reserva}&acao=confirmar";
    $link_recusar = "http://localhost/projeto-reserva-salas/reservas_teste_email/reposta.php?id={$id_nova_reserva}&acao=recusar";

    $mail->Body = "
    <div style='background-color: #f4f6f9; padding: 20px; font-family: Arial, sans-serif;'>
      <div style='background: #ffffff; max-width: 600px; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0px 8px 20px rgba(0,0,0,0.1);'>
        <h2 style='color: #1daad1; text-align: center;'>Nova Solicitação de Reserva - EMUFRN</h2>
        <hr style='border: none; border-top: 2px solid #eee; margin: 20px 0;'>
  
        <p><strong>Nome:</strong> $nome</p>
        <p><strong>Instrumento:</strong> $instrumento</p>
        <p><strong>Matrícula:</strong> $matricula</p>
        <p><strong>E-mail:</strong> $email</p>
        <p><strong>Espaço:</strong> $sala</p>
        <p><strong>Tipo de Evento:</strong> $tipo_evento</p>
        <p><strong>Perfil:</strong> $perfil</p>
        <p><strong>Dia:</strong> $dia</p>
        <p><strong>Horário:</strong> $horario</p>
        <p><strong>Observações:</strong> $obs</p>
  
        <div style='text-align: center; margin-top: 30px;'>
          <a href='$link_confirmar' style='padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 6px; margin-right: 10px;'>✅ Confirmar Reserva</a>
          <a href='$link_recusar' style='padding: 12px 24px; background-color: #f44336; color: white; text-decoration: none; border-radius: 6px;'>❌ Recusar Reserva</a>
        </div>
  
        <p style='text-align: center; color: #888; margin-top: 30px; font-size: 12px;'>Sistema de Reservas - Escola de Música UFRN</p>
      </div>
    </div>
  ";
  

    $mail->send();
    $enviado = true;
} catch (Exception $e) {
    $enviado = false;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Confirmação de Reserva</title>
  <style>
    body {
      background: #f4f6f9;
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 50px;
    }
    .card {
      background: white;
      max-width: 500px;
      margin: auto;
      padding: 40px 30px;
      border-radius: 10px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
    }
    h1 {
      color: #1daad1;
      font-size: 26px;
      margin-bottom: 20px;
    }
    p {
      color: #555;
      font-size: 16px;
      margin-bottom: 30px;
    }
    a.button {
      display: inline-block;
      background: linear-gradient(135deg, #1daad1 0%, #1596a8 100%);
      color: white;
      padding: 12px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      transition: background 0.3s;
    }
    a.button:hover {
      background: linear-gradient(135deg, #1596a8 0%, #117788 100%);
    }
  </style>
</head>
<body>

<div class="card">
  <?php if ($salvo_no_banco): ?>
    <h1>Reserva enviada com sucesso!</h1>
    <p>Sua reserva foi registrada. <?php echo ($enviado ? "E o e-mail foi enviado com sucesso!" : "Porém, não conseguimos enviar o e-mail de confirmação."); ?></p>
  <?php else: ?>
    <h1>Erro ao registrar reserva!</h1>
    <p>Infelizmente não conseguimos salvar sua reserva. Tente novamente mais tarde.</p>
  <?php endif; ?>

  <a class="button" href="index.html">Voltar à Página Inicial</a>
</div>

</body>
</html>
