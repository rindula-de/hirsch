<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/index', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/disableMenu', name: 'app_admin_disable_menu')]
    public function disableMenu(): Response
    {
        $cache = new FilesystemAdapter();
        $cache->deleteItem('menu_disabled');

        $cache->get('menu_disabled', function (ItemInterface $item) {
            $item->expiresAt(new \DateTime('tomorrow midnight'));

            return true;
        });

        $this->addFlash('success', 'admin.notification.menu_disabled');

        return $this->redirectToRoute('app_admin_index');
    }
}
