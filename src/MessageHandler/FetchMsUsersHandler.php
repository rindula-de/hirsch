<?php

namespace App\MessageHandler;

use App\Entity\MsUser;
use App\Message\FetchMsUsers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FetchMsUsersHandler implements MessageHandlerInterface
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function __invoke(FetchMsUsers $message)
    {
        // Login to the Microsoft API
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://login.microsoftonline.com/'.$_ENV['MS_GRAPH_TENANT'].'/oauth2/v2.0/token', [
            'form_params' => [
                'client_id'     => $_ENV['MS_GRAPH_CLIENT_ID'],
                'client_secret' => $_ENV['MS_GRAPH_CLIENT_SECRET'],
                'scope'         => 'https://graph.microsoft.com/.default',
                'grant_type'    => 'client_credentials',
            ],
        ]);
        $token = json_decode($response->getBody(), true);
        // Use MS Graph API to query all users in the tenant
        // https://graph.microsoft.com/v1.0/users
        // Use curl to make the request
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://graph.microsoft.com/v1.0/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Authorization: '.$token['token_type'].' '.$token['access_token'],
                'Cache-Control: no-cache',
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'cURL Error #: '.$err;

            throw new \Exception($message);
        } else {
            $response = json_decode($response, true)['value'];
            $message = 'Successfully fetched users from MS Graph API';

            $entityManager = $this->doctrine->getManager();
            // truncate ms_user table
            $entityManager->getConnection()->executeUpdate('TRUNCATE TABLE ms_user');

            $data = array_filter($response, function ($user) {
                return strpos($user['userPrincipalName'], '#EXT#@') === false && !empty($user['mail']) && !empty($user['givenName']) && !empty($user['surname']) && strpos($user['userPrincipalName'], '@hochwarth-it.de') !== false;
            });

            foreach ($data as $d) {
                $e = new MsUser();
                $e->setName($d['givenName'].' '.$d['surname']);
                $e->setEmail($d['mail']);
                $e->setUid($d['id']);
                if (!$entityManager->isOpen()) {
                    $entityManager = $entityManager->create(
                        $entityManager->getConnection(),
                        $entityManager->getConfiguration()
                    );
                }
                $entityManager->persist($e);
            }
            if (!$entityManager->isOpen()) {
                $entityManager = $entityManager->create(
                    $entityManager->getConnection(),
                    $entityManager->getConfiguration()
                );
            }
            $entityManager->flush();
        }
    }
}
