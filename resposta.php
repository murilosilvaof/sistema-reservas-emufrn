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
            $client->setAuthConfig('credenciais.json'); //
            $client->addScope(Google_Service_Calendar::CALENDAR);
            $client->setAccessType('offline');

            // Token salvo
            $tokenPath = 'token.json';
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }

            $calendar = new Google_Service_Calendar($client);

            // Monta horário corretamente no fuso de Fortaleza
            $inicio_timestamp = strtotime($reserva['dia'] . ' ' . $reserva['horario']);
            $inicio = date('c', $inicio_timestamp);
            $fim    = date('c', $inicio_timestamp + 3600); // Duração padrão de 1 hora

            // Monta evento
            $evento = new Google_Service_Calendar_Event([
                'summary'     => $reserva['obs'] ?: 'Reserva Confirmada - EMUFRN',
                'location'    => $reserva['sala'] ?: 'Local não informado',
                'description' => "Responsável: {$reserva['nome']} ({$reserva['email']})",
                'start'       => ['dateTime' => $inicio, 'timeZone' => 'America/Fortaleza'],
                'end'         => ['dateTime' => $fim,    'timeZone' => 'America/Fortaleza'],
            ]);

            // --- INÍCIO DA ALTERAÇÃO PARA SELECIONAR A AGENDA CORRETA ---

            $calendarId = ''; // Variável para armazenar o ID do calendário correto

            // Mapeia o nome da sala (do banco de dados) para o ID da agenda do Google Calendar
            switch ($reserva['sala']) {
                case 'Auditório Onofre Lopes':
                    $calendarId = 'edcn2dfp7pcmkmtjcpfe4rufao@group.calendar.google.com'; // ID do Calendário Onofre
                    break;
                case 'Auditório Oriano de Almeida':
                    $calendarId = 'vs8dio4ogg75iaaar4ku1nmrjo@group.calendar.google.com'; // ID do Calendário Oriano
                    break;
                default:
                    // Caso a sala não seja uma das esperadas, usa o calendário de produção geral como fallback.
                    $calendarId = 'producao@musica.ufrn.br';
                    // Opcional: Registrar em log para saber se há salas não mapeadas ou nomes inconsistentes.
                    error_log("Sala '{$reserva['sala']}' não mapeada para calendário específico. Usando fallback '{$calendarId}'.");
                    break;
            }

            // --- FIM DA ALTERAÇÃO ---

            if ($calendarId) { // Garante que um calendarId foi definido antes de tentar inserir
                try {
                    $calendar->events->insert($calendarId, $evento);
                    // Se você também quiser que o evento apareça na agenda geral de produção
                    // (além da agenda específica da sala), descomente a linha abaixo:
                    // $calendar->events->insert('producao@musica.ufrn.br', $evento);
                } catch (Exception $e) {
                    // Captura o erro da API do Google Calendar e o loga para depuração,
                    // mas não interrompe a execução para o usuário final.
                    error_log("Erro ao inserir evento no Google Calendar para sala '{$reserva['sala']}' (ID: {$calendarId}): " . $e->getMessage());
                }
            } else {
                // Loga se o ID do calendário não foi definido, indicando um problema no mapeamento.
                error_log("Erro: Calendar ID não definido para a sala: '{$reserva['sala']}'. Evento não criado no Google Calendar.");
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
    body {
      background: #f4f6f9;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0; /* Adicionado para garantir que o body ocupe 100vh */
    }
    .card {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 500px; /* Limita a largura do card */
      width: 90%; /* Responsividade do card */
    }
    h1 {
      color: <?= $acao == 'confirmar' ? '#4CAF50' : '#f44336' ?>;
      font-size: 1.8em; /* Ajuste para melhor responsividade */
      margin-bottom: 20px;
    }
    p {
      color: #555;
      font-size: 1em; /* Ajuste para melhor responsividade */
      margin-bottom: 30px;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #1daad1;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.3s ease; /* Transição suave para hover */
    }
    a:hover {
      background-color: #1596a8; /* Cor um pouco mais escura no hover */
    }
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
