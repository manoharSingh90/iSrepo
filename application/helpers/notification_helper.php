<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('push_notification'))
{
    function push_notification($device_id='',$data=array(),$msg='',$badge='0'){
        $CI = & get_instance();
        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key='AAAAeYjAm70:APA91bFiQxQcLIUKmvtHSi22pB-yAXNJdy-rjQ7RrcFzp9_Whmkn1pC9sDGXs33B_bf4j9cUiSCoqR4WdLFuwD2rfCfnPUu5jtjATV11Ube9ce0NPFwTZCHOSwJaRKkmU1NB8xxQalzD';
        $fields = array (
            'registration_ids' => array ($device_id),
            'priority' => 'high',
            'data' => $data,
            'notification' => array('title'=>'iSamplez','body'=>$msg,'badge' => $badge),
            
        );
        //header includes Content type and api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$api_key
        );      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        //print_r($result);
        return $result;
    }
}