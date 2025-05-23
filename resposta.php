<?php
// Conexão com o banco
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservas_emufrn";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$id = intval($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

if ($id > 0 && ($acao == 'confirmar' || $acao == 'recusar')) {
    $novo_status = $acao == 'confirmar' ? 'confirmada' : 'recusada';
    
    $stmt = $conn->prepare("UPDATE reservas_espacos SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $id);
    $executado = $stmt->execute();
    $stmt->close();
} else {
    $executado = false;
}

$conn->close();
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
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .card {
      background: white;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
      max-width: 500px;
      text-align: center;
    }
    h1 {
      color: <?php echo ($acao == 'confirmar') ? '#4CAF50' : '#f44336'; ?>;
      font-size: 28px;
      margin-bottom: 20px;
    }
    p {
      font-size: 18px;
      color: #555;
      margin-bottom: 30px;
    }
    a.button {
      display: inline-block;
      padding: 12px 30px;
      background-color: #1daad1;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      transition: background 0.3s;
    }
    a.button:hover {
      background-color: #1596a8;
    }
  </style>
</head>
<body>

<div class="card">
  <?php if ($executado): ?>
    <h1><?php echo ($acao == 'confirmar') ? 'Reserva Confirmada!' : 'Reserva Recusada!'; ?></h1>
    <p>A ação foi registrada com sucesso no sistema.</p>
  <?php else: ?>
    <h1>Erro na ação!</h1>
    <p>Não conseguimos processar sua solicitação.</p>
  <?php endif; ?>

  <a class="button" href="index.html">Voltar para o site</a>
</div>

</body>
</html>
