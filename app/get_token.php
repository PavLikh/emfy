<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/app/OAuth2.php");

use AmoCRM\OAuth2\Client\Provider\AmoCRM;

include_once 'vendor/autoload.php';

if (isset($_GET['referer'])) {
    $provider->setBaseDomain($_GET['referer']);
}

if(!empty($unauthorized)) {
    if (!isset($_GET['code'])) {
        /**
         * Просто отображаем кнопку авторизации или получаем ссылку для авторизации
         * По-умолчанию - отображаем кнопку
         */
        $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
        if (true) {
            echo '<div>
                <script
                    class="kommo_oauth"
                    charset="utf-8"
                    data-client-id="' . $provider->getClientId() . '"
                    data-title="Button"
                    data-compact="false"
                    data-class-name="className"
                    data-color="default"
                    data-state="' . $_SESSION['oauth2state'] . '"
                    data-error-callback="handleOauthError"
                    src="https://www.kommo.com/auth/button.js"
                ></script>
                </div>';
            echo '<script>
            handleOauthError = function(event) {
                alert(\'ID клиента - \' + event.client_id + \' Ошибка - \' + event.error);
            }
            </script>';
            die;
        } else {
            $authorizationUrl = $provider->getAuthorizationUrl(['state' => $_SESSION['oauth2state']]);
            header('Location: ' . $authorizationUrl);
        }
    } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
    }

    /**
     * Ловим обратный код
     */
    try {
        /** @var \League\OAuth2\Client\Token\AccessToken $access_token */
        $accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\AuthorizationCode(), [
            'code' => $_GET['code'],
        ]);

        if (!$accessToken->hasExpired()) {
            saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $provider->getBaseDomain(),
            ]);
        }
    } catch (Exception $e) {
        die((string)$e);
    }

    $unauthorized = false;
    /** @var \AmoCRM\OAuth2\Client\Provider\AmoCRMResourceOwner $ownerDetails */
    // $ownerDetails = $provider->getResourceOwner($accessToken);

    // printf('Hello, %s!', $ownerDetails->getName());
    
    
} else {
    $accessToken = getToken();

    $provider->setBaseDomain($accessToken->getValues()['baseDomain']);

    /**
     * Проверяем активен ли токен и делаем запрос или обновляем токен
     */
    if ($accessToken->hasExpired()) {
        /**
         * Получаем токен по рефрешу
         */
        try {
            $accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);

            saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $provider->getBaseDomain(),
            ]);

        } catch (Exception $e) {
            die((string)$e);
        }
    }

}
    
$token = $accessToken->getToken();

try {
    /**
     * Делаем запрос к АПИ
     */
    $data = $provider->getHttpClient()
        ->request('GET', $provider->urlAccount() . 'api/v2/account', [
            'headers' => $provider->getHeaders($accessToken)
        ]);

    $parsedBody = json_decode($data->getBody()->getContents(), true);
    printf('ID аккаунта - %s, название - %s', $parsedBody['id'], $parsedBody['name']);

} catch (GuzzleHttp\Exception\GuzzleException $e) {
    $unauthorized = true;
    // var_dump((string)$e);
}