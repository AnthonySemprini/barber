<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\ProduitCommande;
use App\Entity\Produit;
use App\Form\CommandeFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/commande/confirmation', name: 'app_commande_confirmation')]
    public function createFormCommande(EntityManagerInterface $entityManager, Request $request,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle 'USER'. Si ce n'est pas le cas, l'accès à cette fonction est refusé.
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupère le panier de la session
        $panier = $session->get('panier', []);
        
        // Récupère l'utilisateur actuellement connecté.
        $user = $this->getUser();

        // Crée un nouvel objet DateTime
        $now = new \DateTime();

        // Crée une nouvelle instance de la classe Commande.
        $commande = new Commande();
        // Attribue l'utilisateur connecté à la commande.
        $commande->setUser($user);
        // Définit la date de la commande 
        $commande->setDateCommande($now);
         // Crée un formulaire pour l'objet Commande.
        $form = $this->createForm(CommandeFormType::class, $commande);
        // Gère la requête HTTP actuelle et initialise le formulaire.
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            // Persiste et enregistre l'objet Commande dans la base de données.
            $entityManager->persist($commande);
            $entityManager->flush();
            
        
            // Parcourt chaque élément du panier.
            foreach($panier as $prod => $qtt){
            $produit = $produitRepository->find($prod);
            // Crée une nouvelle instance de ProduitCommande.
            $produitCommande = new ProduitCommande();
            // Attribue le produit et la quantité à ProduitCommande.
            $produitCommande->setProduit($produit);
            $produitCommande->setQuantite($qtt);
            $produitCommande->setCommande($commande);
            
            // Persiste et enregistre l'objet ProduitCommande dans la base de données.
            $entityManager->persist($produitCommande);
            $entityManager->flush();
        }
        // Redirige l'utilisateur vers la page de paiement de la commande.
        return $this->redirectToRoute('app_commande_paiement');
    }
       
        // Si le panier est vide, redirige l'utilisateur vers la page d'accueil.
        if($panier === []){
            return $this->redirectToRoute('app_home');
        }
     
        // Rendu de la vue avec le formulaire de commande.
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'commandeForm' => $form->createView()
        ]);
    }
    
    #[Route('/commande/paiement', name: 'app_commande_paiement')]
    public function paiement()
    {

        return $this->render('commande/valid.html.twig');
    }


}