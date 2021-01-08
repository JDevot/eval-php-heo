<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/entreprise")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
 */
class EntrepriseController extends AbstractController
{
    /**
     * @Route("/", name="entreprise_index", methods={"GET"})
     */
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entrepriseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="entreprise_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('entreprise_index');
        }

        return $this->render('entreprise/new.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entreprise_show", methods={"GET"})
     */
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="entreprise_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Entreprise $entreprise): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entreprise_index');
        }

        return $this->render('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entreprise_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Entreprise $entreprise): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $entreprise->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($entreprise);
                $entityManager->flush();
                $this->addFlash('success', 'Entreprise bien supprimé');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette entreprise car il a des références');
        }
        return $this->redirectToRoute('entreprise_index');
    }
}
