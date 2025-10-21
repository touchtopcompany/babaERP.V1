<?php

namespace App\Utils;

use App\Notifications\LeadMailVerification;
use App\Notifications\LeadMessageVerification;

class NotifyUtil extends Util
{

    protected function beemRequest(string $url, string $method = 'POST', array $postedData = []): bool|string
    {
        //Using Beem
        try {

            $api_key = config('services.beem.key'); //  Your Account SID from beemafrica make it dynamic
            $secret_key = config('services.beem.secret'); //Your Auth Token from app scretekey from beemsecret key

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $option_arr = [
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Basic ' . base64_encode("$api_key:$secret_key"),
                    'Content-Type: application/json'
                )];


            if($method == 'POST' && !empty($postedData)) {
                $option_arr[CURLOPT_POSTFIELDS] =  json_encode($postedData);
                $option_arr[CURLOPT_POST] =  TRUE;
            }

            if($method == 'GET') {
                $option_arr[CURLOPT_HTTPGET] = TRUE;
            }

            curl_setopt_array($ch, $option_arr);
            $response = curl_exec($ch);
            if ($response === FALSE) {
                return 'failed';
            }
            return $response;
        } catch (\Exception $e) {
            return 'failed';
        }
    }

    public function notifyThroughMessage(array $data)
    {
        $source_addr = config('services.beem.address'); //senders name from beemafrica

        $url = config('services.beem.url');

        $postData = array(
            'source_addr' => $source_addr,
            'encoding' => 0,
            'schedule_time' => "",
            'message' => strval($data['message']),
            'recipients' => [array('recipient_id' => "1", 'dest_addr' => $data['phone'])]
        );

        return $this->beemRequest(url: $url, postedData:  $postData);
    }

    public function checkBalance()
    {
        $url = config('services.beem.balance_url');

        return json_decode($this->beemRequest(url: $url, method: 'GET'));
    }

    public function notifyThroughMail($lead): void
    {
        \Notification::route('mail', $lead['email'])
            ->notify(new LeadMailVerification($lead));
    }
}