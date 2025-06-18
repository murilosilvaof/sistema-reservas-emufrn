<?php
require __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('credenciais.json');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');

// Carrega token salvo
$tokenPath = 'token.json';
$accessToken = json_decode(file_get_contents($tokenPath), true);
$client->setAccessToken($accessToken);

// Verifica expiração
if ($client->isAccessTokenExpired()) {
    // Token expirado, tenta renovar
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
}

$calendar = new Google_Service_Calendar($client);

// === DADOS DO EVENTO ===
// Substitua por dados do sistema
$nomeEvento = $_POST['observacao']; // nome do evento
$local = $_POST['local'];           // local do evento
$responsavel = $_POST['responsavel']; 
$data = $_POST['data'];             // formato: yyyy-mm-dd
$horaInicio = $_POST['hora_inicio']; // ex: "14:00"
$horaFim = $_POST['hora_fim'];       // ex: "16:00"

// Monta datas com fuso horário
$inicio = "$data" . "T$horaInicio:00-03:00";
$fim    = "$data" . "T$horaFim:00-03:00";

$evento = new Google_Service_Calendar_Event([
    'summary' => $nomeEvento,
    'location' => $local,
    'description' => "Responsável: $responsavel",
    'start' => [
        'dateTime' => $inicio,
        'timeZone' => 'America/Fortaleza',
    ],
    'end' => [
        'dateTime' => $fim,
        'timeZone' => 'America/Fortaleza',
    ],
]);

// Envia para o calendário
$calendarId = 'primary'; // ou um ID de calendário específico
$event = $calendar->events->insert($calendarId, $evento);

$calendarId = 'producao@musica.ufrn.br';
$service->events->insert($calendarId, $evento);


echo "✅ Evento criado: " . $event->htmlLink;
?>
