<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Controller;

use App\Entity\Holidays;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ModalController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SessionInterface $session;
    private HttpClientInterface $client;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack, HttpClientInterface $client)
    {
        $this->doctrine = $doctrine;
        $this->session = $requestStack->getSession();
        $this->client = $client;
    }

    #[Route('/modalInformationText', name: 'modal', methods: ['GET'])]
    public function index(): Response
    {
        // if the current date is between the start and end date of holidays, the modal will be displayed
        $date = new \DateTime();
        // get all holidays
        $holidays = $this->doctrine->getRepository(Holidays::class)->findAll();
        // check if the current date is between the start and end date of holidays
        foreach ($holidays as $holiday) {
            // nullcheck for start and end date
            if ($holiday->getStart() !== null && $holiday->getEnd() !== null) {
                if ($date >= $holiday->getStart() && $date <= $holiday->getEnd() && $this->session->get('last_showed_holidays', 0) < time() - 60 * 15) {
                    $this->session->set('last_showed_holidays', time());

                    return new Response('Der Hirsch hat aktuell Urlaub und ist vom '.$holiday->getStart()->format('d.m.Y').' bis zum '.$holiday->getEnd()->format('d.m.Y').' nicht verfügbar. Bestellungen können danach wieder aufgenommen werden!');
                }
            }
        }

        return new Response();
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/modalChangelog', name: 'changelog_modal', methods: ['GET'])]
    public function changelog(Request $request): Response
    {
        $cookieName = 'changelogVersion';
        $version = $request->cookies->get($cookieName) ?? 'v2.7.2';
        $page = 0;
        $loadNextPage = true;
        $cache = new FilesystemAdapter();
        $changelog = '';
        do {
            $githubResponseList = $cache->get('releaseList'.$page, function (ItemInterface $item) use ($page) {
                return $this->getRequestListForPage($page);
            });

            foreach ($githubResponseList as $item) {
                if ($item['tag_name'] == $version) {
                    $loadNextPage = false;
                    break;
                }
                if (!is_string($item['tag_name']) || !is_string($item['body'])) {
                    throw new \LogicException('Array definition not up to date');
                }

                $pattern = '/\@[^\s]*?\s\(\#\d*\)/';
                $changelog .= '# '.$item['tag_name']."\r\n\r\n".preg_replace($pattern, '', $item['body']);
            }
            $page++;
        } while ($loadNextPage && $page < 10);

        $response = $this->render('modal/changelog.html.twig', compact('changelog'));
        $response->headers->setCookie(new Cookie($cookieName, $_ENV['APP_VERSION'], httpOnly: false, expire: time() + (365 * 60 * 60 * 24)));

        return $response;
    }

    /**
     * @param int $page
     *
     * @throws TransportExceptionInterface
     *
     * @return array<int,array<string,string|array<string,string|int>|int>>
     */
    private function getRequestListForPage(int $page)
    {
        $githubUrl = 'https://api.github.com/repos/Rindula/hirsch/releases?page=';
        $response = $this->client->request('GET', $githubUrl.$page);

        if ($response->getStatusCode() != Response::HTTP_OK) {
            throw new NotFoundHttpException('Url not found');
        }

        try {
            $githubResponseList = $response->toArray();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|
        ServerExceptionInterface|TransportExceptionInterface $e) {
            throw new NotFoundHttpException('Can not convert API response to array', $e);
        } finally {
            $response->cancel();
        }

        return $githubResponseList;
    }
}
