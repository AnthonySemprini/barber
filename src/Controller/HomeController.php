<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository, PrestationRepository $prestationRepository):Response
    {
        $prestations = $prestationRepository->findWithStar();// Récupére les prestations star

        $produits = $produitRepository->findLastThreeProd(); // Recupére les 3 derniers produits ajoute en bdd
        
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'prestations' => $prestations,
                'produits' => $produits,
            ]);
    }

 
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        //redirige page profil user
        return $this->render('home/profil.html.twig');
    }
    #[Route('/historiqueCommande', name: 'app_historique_commandes')]
    public function historiqueCommandes(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        $commandes = $entityManager->getRepository(Commande::class)->findBy(['User' => $user]);

        return $this->render('home/historiqueCommande.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
