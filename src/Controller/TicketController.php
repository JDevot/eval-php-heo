<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Demande;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    protected $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }
    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {

        if ($this->auth->isGranted('ROLE_ADMIN')) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findAll(),
            ]);
        } else {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findByClient($this->getUser()->getId()),
            ]);
        }
    }
    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/new/{id}", name="ticket_new_demande", methods={"GET","POST"})
     */
    public function newByDemande(Request $request, Demande $demande): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
        $demande->addTicket($ticket);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_UTILISATEUR')")
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($ticket);
                $entityManager->flush();
                $this->addFlash('success', 'ticket bien supprimé');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce ticket car il a des références');
        }
        return $this->redirectToRoute('ticket_index');
    }
}
