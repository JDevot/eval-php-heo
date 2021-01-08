<?php

namespace App\DataFixtures;
use App\Entity\Ticket;
use App\Entity\Demande;
use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use app\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Faker;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    private $userRepository;
    private $demandeRepository;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, DemandeRepository $demandeRepository)
    {
        $this->demandeRepository = $demandeRepository;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        $this->createUser($manager, 10);
        $this->createDemande($manager, 10);
        $this->createTicket($manager, 10);
        $this->createEntreprise($manager, 10);
    }

    public function createUser($manager, $nb)
    {
        $faker = Faker\Factory::create('fr_FR');
       
        // Création d'un user role admin
        $admin = new User();
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'azerty'
        ));
        $admin->setActif(TRUE);
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setEmail('admin@test.com');
        $manager->persist($admin);

        // Créattion d'un user role client
        $customer = new User();
        $customer->setPassword($this->passwordEncoder->encodePassword(
            $customer,
            'azerty'
        ));
        $customer->setActif(TRUE);
        $customer->setRoles(["ROLE_CUSTOMER"]);
        $customer->setEmail('client@test.com');
        $manager->persist($customer);

        // Création d'un user role utilisateur
        $utilisateur = new User();
        $utilisateur->setPassword($this->passwordEncoder->encodePassword(
            $utilisateur,
            'azerty'
        ));
        $utilisateur->setActif(TRUE);
        $utilisateur->setRoles(["ROLE_UTILISATEUR"]);
        $utilisateur->setEmail('utilisateur@test.com');
        $manager->persist($utilisateur);

        // Création d'utilisateur avec des données aléatoire 
        for ($i = 0; $i < $nb; $i++) {
            $user = new User();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'azerty'
            ));
            $user->setActif(TRUE);
            $user->setRoles($faker->randomElements($array = array('ROLE_ADMIN', 'ROLE_UTILISATEUR', 'ROLE_CUSTOMER')));
            $user->setEmail($faker->email);
            $manager->persist($user);
        }
        $manager->flush();
    }
    // Création de demande
    public function createDemande($manager, $nb){
        $faker = Faker\Factory::create('fr_FR');

        $repo = $this->userRepository->findAll();

        for ($i = 0; $i < $nb; $i++) {
            $demande = new Demande();
            $demande->setContent($faker->text);
            $demande->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $randomUser = $repo[$faker->biasedNumberBetween($min = 0, $max = count($repo)-1, $function = 'sqrt')];
            $demande->setIdProprietaire($randomUser->getId());
            $demande->addUser($randomUser);
            $demande->addUser($repo[$faker->biasedNumberBetween($min = 0, $max = count($repo)-1, $function = 'sqrt')]);
            $manager->persist($demande);
        }
        $manager->flush();
    }
    // création de ticket
    public function createTicket($manager, $nb){
        $faker = Faker\Factory::create('fr_FR');

        $repo = $this->demandeRepository->findAll();

        for ($i = 0; $i < $nb; $i++) {
            $ticket = new Ticket();
            $ticket->setContent($faker->text);
            $ticket->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $ticket->setEtat($faker->randomElement($array = array('encours', 'afaire', 'clos','archiver')));
            $ticket->setDemande($repo[$faker->biasedNumberBetween($min = 0, $max = count($repo)-1, $function = 'sqrt')]);
            $manager->persist($ticket);
        }
        $manager->flush();
    }
    // création d'entreprise
    public function createEntreprise($manager, $nb){
        $faker = Faker\Factory::create('fr_FR');


        for ($i = 0; $i < $nb; $i++) {
            $entreprise = new Entreprise();
            $entreprise->setName($faker->sentence($nbWords = 6, $variableNbWords = true));
            $entreprise->setAdress($faker->text);
            $manager->persist($entreprise);
        }
        $manager->flush();
    }


}
