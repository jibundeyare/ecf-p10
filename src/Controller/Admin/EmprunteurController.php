<?php

namespace App\Controller\Admin;

use App\Entity\Emprunteur;
use App\Form\EmprunteurType;
use App\Repository\EmprunteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/emprunteur')]
class EmprunteurController extends AbstractController
{
    #[Route('/', name: 'app_admin_emprunteur_index', methods: ['GET'])]
    public function index(EmprunteurRepository $emprunteurRepository): Response
    {
        return $this->render('admin/emprunteur/index.html.twig', [
            'emprunteurs' => $emprunteurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_emprunteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmprunteurRepository $emprunteurRepository): Response
    {
        $emprunteur = new Emprunteur();
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emprunteurRepository->save($emprunteur, true);

            return $this->redirectToRoute('app_admin_emprunteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/emprunteur/new.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_emprunteur_show', methods: ['GET'])]
    public function show(Emprunteur $emprunteur): Response
    {
        return $this->render('admin/emprunteur/show.html.twig', [
            'emprunteur' => $emprunteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_emprunteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunteur $emprunteur, EmprunteurRepository $emprunteurRepository): Response
    {
        $form = $this->createForm(EmprunteurType::class, $emprunteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emprunteurRepository->save($emprunteur, true);

            return $this->redirectToRoute('app_admin_emprunteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/emprunteur/edit.html.twig', [
            'emprunteur' => $emprunteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_emprunteur_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunteur $emprunteur, EmprunteurRepository $emprunteurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunteur->getId(), $request->request->get('_token'))) {
            $emprunteurRepository->remove($emprunteur, true);
        }

        return $this->redirectToRoute('app_admin_emprunteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
