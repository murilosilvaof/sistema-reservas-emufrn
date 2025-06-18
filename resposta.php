<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Define o timezone para Fortaleza, garantindo que date('c') use -03:00
date_default_timezone_set('America/Fortaleza');

// Carregar Composer
require __DIR__ . '/vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;

// Configuração do banco (Hostinger)
$servername = "localhost";
$username   = "u563793805_marcato_emufrn";
$password   = "Cceemufrn@2022";
$dbname     = "u563793805_marcato_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro ao conectar ao banco: " . $conn->connect_error);
}

$id = intval($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';
$executado = false;

if ($id > 0 && in_array($acao, ['confirmar', 'recusar'])) {
    $novo_status = $acao === 'confirmar' ? 'confirmada' : 'recusada';

    // Atualiza status no banco
    $stmt = $conn->prepare("UPDATE reservas_espacos SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $id);
    $executado = $stmt->execute();
    $stmt->close();

    // Se for confirmar, tenta enviar ao Google Calendar
    if ($acao === 'confirmar' && $executado) {
        $res = $conn->query("SELECT * FROM reservas_espacos WHERE id = $id");
        if ($res && $res->num_rows > 0) {
            $reserva = $res->fetch_assoc();

            // Inicializa cliente Google
            $client = new Google_Client();
            $client->setAuthConfig('credenciais.json');
            $client->addScope(Google_Service_Calendar::CALENDAR);
            $client->setAccessType('offline');

            // Token salvo
            $token = json_decode(file_get_contents('token.json'), true);
            $client->setAccessToken($token);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents('token.json', json_encode($client->getAccessToken()));
            }

            $calendar = new Google_Service_Calendar($client);

            // Monta horário corretamente no fuso de Fortaleza
            $inicio_timestamp = strtotime($reserva['dia'] . ' ' . $reserva['horario']);
            $inicio = date('c', $inicio_timestamp);
            $fim    = date('c', $inicio_timestamp + 3600);

            // Monta evento
            $evento = new Google_Service_Calendar_Event([
                'summary'     => $reserva['obs'] ?: 'Reserva Confirmada - EMUFRN',
                'location'    => $reserva['sala'] ?: 'Local não informado',
                'description' => "Responsável: {$reserva['nome']} ({$reserva['email']})",
                'start'       => ['dateTime' => $inicio, 'timeZone' => 'America/Fortaleza'],
                'end'         => ['dateTime' => $fim,    'timeZone' => 'America/Fortaleza'],
            ]);

            try {
                $calendarId = 'producao@musica.ufrn.br';
                $calendar->events->insert($calendarId, $evento);
            } catch (Exception $e) {
                // Ignora erro para não quebrar a interface
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Confirmação de Reserva</title>
  <style>
    body { background: #f4f6f9; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
    h1 { color: <?= $acao == 'confirmar' ? '#4CAF50' : '#f44336' ?>; }
    a { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #1daad1; color: white; text-decoration: none; border-radius: 6px; }
  </style>
</head>
<body>
  <div class="card">
    <h1><?= $executado ? ($acao == 'confirmar' ? 'Reserva Confirmada!' : 'Reserva Recusada!') : 'Erro na Ação!' ?></h1>
    <p><?= $executado ? 'A ação foi registrada no sistema.' : 'Não conseguimos processar a solicitação.' ?></p>
    <a href="https://musica.ufrn.br">Voltar para o site</a>
  </div>
</body>
</html>
