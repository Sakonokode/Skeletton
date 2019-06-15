<?php

declare(strict_types=1);

namespace Skeletton\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package Skeletton\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="skeletton_homepage")
     *
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->renderView('index.html.twig'));
    }
}