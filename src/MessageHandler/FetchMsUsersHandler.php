<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\MessageHandler;

use App\Entity\MsUser;
use App\Message\FetchMsUsers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FetchMsUsersHandler
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function __invoke(FetchMsUsers $message): void
    {
        // Login to the Microsoft API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://login.microsoftonline.com/'.$_ENV['MS_GRAPH_TENANT'].'/oauth2/v2.0/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $_ENV['MS_GRAPH_CLIENT_ID'],
                'client_secret' => $_ENV['MS_GRAPH_CLIENT_SECRET'],
                'scope' => 'https://graph.microsoft.com/.default',
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        if (is_string($response)) {
            $token = json_decode($response, true);

            if (is_array($token)) {
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
                    $message = 'cURL Error #: '.$err;

                    throw new \Exception($message);
                } elseif (is_string($response)) {
                    $response = json_decode($response, true);
                    if (is_array($response)) {
                        $response = $response['value'];
                        if (is_array($response)) {
                            $message = 'Successfully fetched users from MS Graph API';

                            /** @var EntityManagerInterface */
                            $entityManager = $this->doctrine->getManager();
                            // truncate ms_user table
                            $entityManager->getConnection()->executeStatement('TRUNCATE TABLE ms_user');

                            $data = array_filter($response, function ($user) {
                                /* @var string[] $user */
                                return false === strpos($user['userPrincipalName'], '#EXT#@') && !empty($user['mail']) && !empty($user['givenName']) && !empty($user['surname']) && false !== strpos($user['userPrincipalName'], '@hochwarth-it.de');
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
            }
        }
    }
}
