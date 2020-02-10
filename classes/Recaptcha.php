<?php

class Recaptcha {
    static function verify($responseToken)
    {
        $data = array(
            "secret" => Config::get("recaptcha/secret"),
            'response' => $responseToken,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($curl, CURL_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $jsonObj = json_decode($response);

        return $jsonObj->success;
    }
}