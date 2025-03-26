<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VolController extends AbstractController
{
    #[Route('/vol', name: 'app_vol')]
    public function index(): Response
    {
        return $this->render('vol/index.html.twig', [
            'controller_name' => 'VolController',
        ]);
    }
    #[Route('/vol/{id}', name: 'vol_detail')]
    public function detail(int $id): Response
    {
        return new Response("Détails du vol numéro : $id");
    }
}
