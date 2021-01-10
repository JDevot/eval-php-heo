<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ClientType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;

/**
 * @Route("/user")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
 */
class UserController extends AbstractController
{
    protected $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'template' => 'utilisateur'
        ]);
    }
    /**
     * @Route("/client", name="client_index", methods={"GET"})
     */
    public function indexClient(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findUsersByRole('ROLE_CUSTOMER'),
            'template' => 'client'
        ]);
    }
    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($form->get('plainPassword')) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'template' => 'utilisateur'
        ]);
    }
    /**
     * @Route("/clientnew", name="client_new", methods={"GET","POST"})
     */
    public function clientnew(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(ClientType::class, $user);
        $user->setRoles(array('ROLE_CUSTOMER'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($form->get('plainPassword')) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('client_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'template' => 'client'
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'template' => 'utilisateur'
        ]);
    }
    /**
     * @Route("/client/{id}", name="client_show", methods={"GET"})
     */
    public function clientShow(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'template' => 'client'
        ]);
    }
    /**
     * @Route("/{id}/editclient", name="client_edit", methods={"GET","POST"})
     */
    public function editClient(Request $request, User $user): Response
    {
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('client_index');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'template' => 'client',
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'template' => 'utilisateur',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur bien supprimé');
            }
        } catch (\Exception $e) {
            $this->addFlash('success', 'Utilisateur bien supprimé');
        }
        return $this->redirectToRoute('user_index');
    }
    /**
     * @Route("/client/{id}", name="client_delete", methods={"DELETE"})
     */
    public function ClientDelete(Request $request, User $user): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Client bien supprimé');
            }
        } catch (\Exception $e) {
            $this->addFlash('success', 'Client bien supprimé');
        }
        return $this->redirectToRoute('client_index');
    }
}
