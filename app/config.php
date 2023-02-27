<?php

define('TOKEN_FILE', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');

// Integration ID: в настройках виджета
$clientId = 'efeb1bfa-22a0-4a23-8505-84853b3d55e5';
// Secret key: в настройках виджета
$clientSecret = 'f1Yo5FoWo82YxLUkWCKpvnWdygexMO3bmVf3nPN7rClfw2RlMZp74WoCKAeTq9HM';
// $redirectUri = 'https://b5bd-85-249-38-193.eu.ngrok.io/amocrm/get_token.php';
$redirectUri = 'https://b5bd-85-249-38-193.eu.ngrok.io';

$baseDomain = 'https://emfytest01.kommo.com/'; // или yoursubdomain.amocrm.ru - ваша страница
// $my_secret для проверки доступа, такой же в script.js
$my_secret = 'aass'; // не использую--
