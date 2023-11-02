<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\ProduitCommande;
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

    #[Route('/commande/infoClient', name: 'app_commande_infoClient')]
    public function createFormCommande(EntityManagerInterface $entityManager, Request $request,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        //dd($session);
        $user = $this->getUser();
        $now = new \DateTime();
        //dd($user);
        $commande = new Commande();
        $commande->setUser($user);
        $commande->setDateCommande($now);
        
         
    //     $produitCommande = new ProduitCommande();
    
        
    //     $produitsCommande = array_map(function ($item) {
    //         $produitCommande = new ProduitCommande();
    //         $produitCommande->setProduit($produitRepository->find($item['id']));
    //         $produitCommande->setQuantite($item['quantite']);

    //         return $produitCommande;
    //     }, $panier);

    // $panierCommande->addProduits($produitsCommande);
    //     // Persistez l'entité PanierCommande en base de données
    //     $entityManager->persist($panierCommande);

    //     // Flush les modifications
    //     $entityManager->flush();



        $form = $this->createForm(CommandeFormType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
      
            return $this->redirectToRoute('app_commande_paiement');
        }

        if($panier === []){
            //Le panier est vide, on retourne sur la homepage
            return $this->redirectToRoute('app_home');
        }
     
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