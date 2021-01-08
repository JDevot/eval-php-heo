<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Demande;
use App\Form\DemandeType;
use Exception;
use App\Repository\UserRepository;
use App\Repository\DemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/demande")
  * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR') or is_granted('ROLE_CUSTOMER')")
 */
class DemandeController extends AbstractController
{
    protected $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }
    /** 
     * @Route("/", name="demande_index", methods={"GET"})
     */
    public function index(DemandeRepository $demandeRepository,  UserRepository $userRepository): Response
    {     
        $demandes = $demandeRepository->findAll();
        foreach($demandes as $demande){
            $demande->setUser($userRepository->find($demande->getIdProprietaire()));
        }
        if ($this->auth->isGranted('ROLE_ADMIN')) {
            return $this->render('demande/index.html.twig', [
                'demandes' => $demandes,
            ]);
        } else {
            return $this->render('demande/index.html.twig', [
                'demandes' => $demandeRepository->findByClient($this->getUser()->getId()),
            ]);
        }
    }

    /**
     * @Route("/new", name="demande_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $demande = new Demande();
        $user = $this->getUser();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);
        $demande->addUser($user);
        $demande->setIdProprietaire($user->getId());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('demande_index');
        }

        return $this->render('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="demande_show", methods={"GET"})
     */
    public function show(Demande $demande,UserRepository $userRepository): Response
    {
        $demande->setUser($userRepository->find($demande->getIdProprietaire()));
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="demande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Demande $demande): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('demande_index');
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="demande_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     */
    public function delete(Request $request, Demande $demande): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $demande->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($demande);
                $entityManager->flush();
                $this->addFlash('success', 'demande bien supprimé');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette demande car il a des références');
        }
        return $this->redirectToRoute('demande_index');
    }
}
