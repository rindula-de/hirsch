<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Repository\HolidaysRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModalController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    #[Route('/modalInformationText', name: 'modal', methods: ['GET'])]
    public function index(HolidaysRepository $holidaysRepository, TranslatorInterface $translator): Response
    {
        // if the current date is between the start and end date of holidays, the modal will be displayed
        $date = new \DateTime();

        // get all holidays
        $holidays = $holidaysRepository->findAll();

        // check if the current date is between the start and end date of holidays
        foreach ($holidays as $holiday) {
            // nullcheck for start and end date
            if (null !== $holiday->getStart() && null !== $holiday->getEnd()) {
                if (
                    $date >= $holiday->getStart()
                    && $date <= $holiday->getEnd()
                    && $this->session->get('last_showed_holidays', 0) < time() - 60 * 15
                ) {
                    $this->session->set('last_showed_holidays', time());

                    return new Response(
                        $translator->trans('holidays.in_holiday', [
                            '%start%' => $holiday->getStart()->format('d.m.Y'),
                            '%end%' => $holiday->getEnd()->format('d.m.Y'),
                        ])
                    );
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

                $pattern = '/^- ([[:print:]]+?) \@[^\s]+?\s\(\#(\d*)\)/m';
                $changelog .= '# '.$item['tag_name']."\r\n\r\n".preg_replace($pattern, "- $1 [[Ansehen]](https://github.com/rindula/hirsch/pull/$2)\r\n", $item['body']);
            }
            ++$page;
        } while ($loadNextPage && $page < 10);

        $response = $this->render('modal/changelog.html.twig', compact('changelog'));
        $response->headers->setCookie(new Cookie($cookieName, $_ENV['APP_VERSION'], expire: time() + (365 * 60 * 60 * 24), httpOnly: false));

        return $response;
    }
}
