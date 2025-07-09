<?php
$config = require 'credentials.php';

$params = [
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
    'access_type' => 'online',
    'include_granted_scopes' => 'true',
    'prompt' => 'select_account',
];

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $auth_url);
exit;