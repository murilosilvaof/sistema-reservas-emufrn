<?php
require 'vendor/autoload.php';

use Google\Client;

function getClient() {
    $client = new Client();
    $client->setApplicationName('EMUFRN Reserva');
    $client->setScopes(Google\Service\Calendar::CALENDAR);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Carregar o token previamente salvo
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // Verificar se o token é válido
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Solicitar autorização
            $authUrl = $client->createAuthUrl();
            echo "Acesse o link: $authUrl\n";
            echo "Digite o código de autenticação: ";
            $authCode = trim(fgets(STDIN));

            // Trocar o código por um token
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Salvar o token para futuras execuções
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
    }
    return $client;
}

$client = getClient();
echo "Token obtido com sucesso!\n";
?>
