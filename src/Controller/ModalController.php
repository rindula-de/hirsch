<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModalController extends AbstractController
{
    /**
     * @Route("/modalInformationText", name="modal")
     */
    public function index(): Response
    {
        return new Response();
    }
}
