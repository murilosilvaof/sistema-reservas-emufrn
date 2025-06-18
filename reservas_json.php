<?php
// reservas_json.php
header('Content-Type: application/json; charset=UTF-8');

$sala = $_GET['sala'] ?? '';
if (!$sala) {
  echo '[]';
  exit;
}

// Conexão MySQL
$mysqli = new mysqli(
  "localhost",
  "u563793805_marcato_emufrn",
  "Cceemufrn@2022",
  "u563793805_marcato_db"
);
if ($mysqli->connect_error) {
  http_response_code(500);
  echo '[]';
  exit;
}

// Busca só reservas confirmadas desta sala
$stmt = $mysqli->prepare("
  SELECT dia, horario
  FROM reservas_espacos
  WHERE sala = ?
    AND status = 'confirmada'
");
$stmt->bind_param("s", $sala);
$stmt->execute();
$result = $stmt->get_result();

$reservas = [];
while ($row = $result->fetch_assoc()) {
  // Garantir formato HH:MM
  $hora = substr($row['horario'], 0, 5);
  $reservas[] = [
    'dia'     => $row['dia'],    // "2025-06-16"
    'horario' => $hora           // "08:00"
  ];
}

echo json_encode($reservas);
