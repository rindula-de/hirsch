<?php

namespace App\MessageHandler;

use App\Message\FetchMsUsers;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FetchMsUsersHandler implements MessageHandlerInterface
{

    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(FetchMsUsers $message)
    {
        // Login to the Microsoft API
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://login.microsoftonline.com/'.$_ENV['MS_GRAPH_TENANT'].'/oauth2/v2.0/token', [
            'form_params' => [
                'client_id' => $_ENV['MS_GRAPH_CLIENT_ID'],
                'client_secret' => $_ENV['MS_GRAPH_CLIENT_SECRET'],
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ],
        ]);
        $token = json_decode($response->getBody(), true);
        // Use MS Graph API to query all users in the tenant
        // https://graph.microsoft.com/v1.0/users
        // Use curl to make the request
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://graph.microsoft.com/v1.0/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: '.$token['token_type'].' '.$token['access_token'],
                'Cache-Control: no-cache',
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'cURL Error #: ' . $err;
            // throw new \Exception($message);
        } else {
            $response = json_decode($response, true);
            $message = 'Successfully fetched users from MS Graph API';
        }
        $this->logger->info($message);
        $this->logger->info(print_r($response, true));

    }
}
