<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vol;


final class VolController extends AbstractController
{
    #[Route('/vol', name: 'app_vol')]
    public function index(): Response
    {
        return $this->render('vol/inscription.html.twig', [
            'controller_name' => 'VolController',
        ]);
    }
    #[Route('/vol/ajout', name: 'ajouter_vol')]
    public function ajouterVol(EntityManagerInterface $entityManager): Response
    {
        $vol = new Vol();
        $vol->setVilleDepart('Paris');
        $vol->setVilleArrivee('Barcelona');
        $vol->setDateDepart(new \DateTime('now'));
        $vol->setHeureDepart(new \DateTime('now'));
        $vol->setPrixBilletInitiale(100);
        $entityManager->persist($vol);
        $entityManager->flush();
        $entityManager->refresh($vol);
        dump($vol);
        return $this->render('vol/ajout.html.twig', [
        'controller_name' => 'VolController',
            'vol' => $vol,
    ]);
    }
    #[Route('/vol/{id}', name: 'vol_detail')]
    public function detail(int $id): Response
    {
        return new Response("Détails du vol numéro : $id");
    }
}
