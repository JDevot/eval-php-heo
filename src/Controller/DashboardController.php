<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Demande;
use App\Entity\Ticket;
use App\Repository\DemandeRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class DashboardController extends AbstractController
{
    protected $auth;

    public function __construct(AuthorizationCheckerInterface $auth){
        $this->auth = $auth;  
    }
    /**
     * @Route("/dashboard", name="dashboard")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function index(UserRepository $user, TicketRepository $ticket, DemandeRepository $demande): Response
    {
        $users = $user->findAll();
        $tickets = $ticket->findAll();
        $demandes = $demande->findAll();
        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
            'tickets'=> $tickets,
            'demandes' => $demandes,
            'controller_name' => 'DashboardController',
        ]);
    }
}
