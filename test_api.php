<?php
require_once 'ApiHelper.php';  // Inclui a classe que você criou

// Suas credenciais de teste (pode colar as da imagem aqui!)
$api_base_url = "https://api.info.ufrn.br";
$api_auth_url = "https://autenticacao.info.ufrn.br/authz-server/oauth/token";
$api_client_id = "emprestimos-escola-musica-Ct0Aa8QB6J392d61";
$api_client_secret = "IdYnZtw3XNasadasdmpkykiFGVDRWPCr3C7yK";
$api_key = "N45QQxVisYX4CUh7lasaegeNTasn4fBkeO3jlP5";

// Exemplo de endpoint (tem que ver com a UFRN qual endpoint usar)
$endpoint = "/v1/seu-endpoint-de-teste";

// Faz a requisição
$response = ApiHelper::get($endpoint, $api_base_url, $api_auth_url, $api_client_id, $api_client_secret, $api_key);

// Mostra o resultado
echo "<pre>";
print_r($response);
echo "</pre>";
?>
