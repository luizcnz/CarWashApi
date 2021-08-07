<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 7/8/2021
 * Time: 01:51
 */

namespace Api\controllers;


class NotificationController
{

    function sendMessage()
    {
        $content = array(
            "en" => 'English Message'
        );

        $fields = array(
            'app_id' => "5eb5a37e-b458-11e3-ac11-000c2940e62c",
            'include_player_ids' => array("6392d91a-b206-4b7b-a620-cd68e32c3a76","76ece62b-bcfe-468c-8a78-839aeaa8c5fa","8e0f21fa-9a5a-4ae7-a9a6-ca1f24294b86"),
            'data' => array("foo" => "bar"),
            'contents' => $content
        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

//    $response = sendMessage();
//    $return["allresponses"] = $response;
//    $return = json_encode( $return);
//
//    print("\n\nJSON received:\n");
//    print($return);
//    print("\n");

}