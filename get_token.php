<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/credenciais.json');
$client->addScope(Google_Service_Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->setRedirectUri('https://ismcursos.com/marcato/get_token.php');

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "<a href='$authUrl'>Clique aqui para autorizar o acesso ao Google Calendar</a>";
    exit;
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        file_put_contents(__DIR__ . '/token.json', json_encode($token));
        echo "<h3 style='color: green;'>✅ Token gerado com sucesso!</h3>";
    } else {
        echo "<h3 style='color: red;'>❌ Erro ao gerar token:</h3>";
        var_dump($token);
    }
}
?>
