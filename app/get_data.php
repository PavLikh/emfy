<?php

// include_once 'config.php';

// if($_GET['my_secret'] != $my_secret) die('нет доступа');

include_once 'OAuth2.php';

if ($_SERVER['DOCUMENT_URI'] == 'ipdata.php') {
    header("Access-Control-Allow-Origin: *");
}

use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Exceptions\AmoCRMApiException;

if(empty($unauthorized)) {
    $accessToken = getToken();

    $apiClient = new \AmoCRM\Client\AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

    $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]
                );
            }
        );


    if (!empty($_GET['lead_id'])) {
        $lead_id = (int)$_GET['lead_id'];

        echo "<table style=\"font-family: arial, sans-serif; border-collapse: collapse; width: 100%; \">
        <caption style=\"font-weight: bold; padding: 8px;\">All Products Lead with id".$lead_id."</caption>
          <tr style=\"background-color: #1b3446;color: #ffffff\">
            <th style=\"border: 1px solid #dddddd; text-align: left; padding: 8px;\">Name</th>
            <th style=\"border: 1px solid #dddddd; text-align: left; padding: 8px;\">Quantity</th>
          </tr>";
        $lead = $apiClient->leads()->getOne($lead_id);

        $links = $apiClient->leads()->getLinks($lead);
        $i=1;
        foreach($links->toArray() as $key=>$value){ $i++;
            if($value['to_entity_type'] == 'catalog_elements'){
            $catalogElement = $apiClient->catalogElements($value['metadata']['catalog_id'])->getOne($value['to_entity_id']);  
            if($i % 2 == 0)echo "<tr>";
            else echo "<tr style=\"background-color: #dddddd;\">";
            echo "<td style=\"border: 1px solid #dddddd; text-align: left; padding: 8px;\">".$catalogElement->toArray()['name']."</td>
            <td style=\"border: 1px solid #dddddd; text-align: left; padding: 8px;\">".$value['metadata']['quantity']."</td>
            </tr>";
            } 
        } 
        echo "</table>";
    }
}

