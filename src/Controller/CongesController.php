<?php

namespace App\Controller;

use App\Entity\Conges;
use App\Form\CongesType;
use App\Repository\CongesRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conges')]
class CongesController extends AbstractController
{
    #[Route('/', name: 'app_conges_index', methods: ['GET'])]
    public function index(CongesRepository $congesRepository): Response
    {
        return $this->render('conges/index.html.twig', [
            'conges' => $congesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_conges_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conges = new Conges();

        // Si l'utilisateur n'est pas admin, on définit l'utilisateur courant
        if (!$this->isGranted('ROLE_ADMIN')) {
            $conges->setUtilisateur($this->getUser());
        }

        $form = $this->createForm(CongesType::class, $conges, [
            'is_admin' => $this->isGranted('ROLE_ADMIN')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pour les non-admins, on s'assure que le statut est null
            if (!$this->isGranted('ROLE_ADMIN')) {
                $conges->setEstValide(null);
            }

            $entityManager->persist($conges);
            $entityManager->flush();

            return $this->redirectToRoute('app_conges_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conges/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_conges_show', methods: ['GET'])]
    public function show(Conges $conges): Response
    {
        return $this->render('conges/show.html.twig', [
            'conges' => $conges,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conges_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conges $conges, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CongesType::class, $conges, [
            'is_admin' => $this->isGranted('ROLE_ADMIN')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $conges->setValidateur($this->getUser());
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le congé a été mis à jour avec succès.');
            return $this->redirectToRoute('app_conges_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conges/edit.html.twig', [
            'conges' => $conges,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_conges_delete', methods: ['POST'])]
    public function delete(Request $request, Conges $conges, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conges->getId(), $request->request->get('_token'))) {
            $entityManager->remove($conges);
            $entityManager->flush();
            $this->addFlash('success', 'Le congé a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_conges_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/demandeConges/{id}', name: 'app_conges_demande')]
    public function demande(Request $request, EntityManagerInterface $em, UtilisateurRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérification que l'utilisateur connecté correspond à l'ID demandé
        if ($this->getUser()->getId() !== $id) {
            return $this->redirectToRoute('app_home');
        }

        $conges = new Conges();
        $conges->setUtilisateur($user);
        $conges->setEstValide(null);

        $form = $this->createForm(CongesType::class, $conges, [
            'is_admin' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($conges);
            $em->flush();

            $this->addFlash('success', 'Votre demande de congé a été envoyée !');
            return $this->redirectToRoute('app_conges_VoirConges', ['id' => $user->getId()]);
        }

        return $this->render('conges/demande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/VoirConges/{id}', name: 'app_conges_VoirConges', methods: ['GET'])]
    public function VoirConges(int $id, CongesRepository $congesRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getId() !== $id) {
            return $this->redirectToRoute('app_home');
        }

        $conges = $congesRepository->findBy(['utilisateur' => $user]);

        return $this->render('conges/VoirConges.html.twig', [
            'conges' => $conges,
        ]);
    }

    #[Route('/valider/{id}', name: 'app_conges_valider', methods: ['POST'])]
    public function valider(Conges $conge, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs');
        }

        $conge->setEstValide(true);
        $conge->setValidateur($this->getUser());
        $em->flush();

        $this->addFlash('success', 'Le congé a été validé avec succès.');
        return $this->redirectToRoute('app_conges_index');
    }

    #[Route('/refuser/{id}', name: 'app_conges_refuser', methods: ['POST'])]
    public function refuser(Conges $conge, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs');
        }

        $conge->setEstValide(false);
        $conge->setValidateur($this->getUser());
        $em->flush();

        $this->addFlash('warning', 'Le congé a été refusé.');
        return $this->redirectToRoute('app_conges_index');
    }

    #[Route('/a-valider', name: 'app_conges_a_valider', methods: ['GET'])]
    public function congesAValider(CongesRepository $congesRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs');
        }

        $conges = $congesRepository->findBy(['estValide' => null]);

        return $this->render('conges/a_valider.html.twig.twig', [
            'conges' => $conges
        ]);
    }
}