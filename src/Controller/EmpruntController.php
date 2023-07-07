<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/emprunt')]
class EmpruntController extends AbstractController
{
    #[Route('/', name: 'app_emprunt_index', methods: ['GET'])]
    public function index(EmpruntRepository $empruntRepository): Response
    {
        $emprunts = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $emprunts = $empruntRepository->findAll();
        } elseif ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            $emprunteur = $user->getEmprunteur();
            $emprunts = $empruntRepository->findByEmprunteur($emprunteur);
        }

        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $emprunts,
        ]);
    }

    #[Route('/new', name: 'app_emprunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmpruntRepository $empruntRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $empruntRepository->save($emprunt, true);

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_show', methods: ['GET'])]
    public function show(Emprunt $emprunt): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser()->getEmprunteur() != $emprunt->getEmprunteur()) {
                throw new AccessDeniedException();
            }
        }

        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emprunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunt $emprunt, EmpruntRepository $empruntRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $empruntRepository->save($emprunt, true);

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunt/edit.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunt $emprunt, EmpruntRepository $empruntRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->request->get('_token'))) {
            $empruntRepository->remove($emprunt, true);
        }

        return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
    }
}
