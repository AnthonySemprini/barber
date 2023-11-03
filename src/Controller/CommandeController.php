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

    #[Route('/commande/infoClient', name: 'app_commande_infoClient')]
    public function createFormCommande(EntityManagerInterface $entityManager, Request $request,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);
        // dd($panier);
        $user = $this->getUser();
        $now = new \DateTime();
        $commande = new Commande();
        $commande->setUser($user);
        $commande->setDateCommande($now);
        
        $form = $this->createForm(CommandeFormType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
            //dd($commande->getId());
        
        
        foreach($panier as $prod => $qtt){
            $produit = $produitRepository->find($prod);
            $produitCommande = new ProduitCommande();
            $produitCommande->setProduit($produit);
            $produitCommande->setCommande($commande);
            $produitCommande->setQuantite($qtt);
            dd($produitCommande);
            //dd($produitCommande);
            
            $entityManager->persist($produitCommande);
            //dd($produitCommande);
            $entityManager->flush();
        }
        // $array = [];
        // foreach($panier as $prod => $qtt)
        // {
        //     $array[] = $produitRepository->find($prod);
        // }
        // dd($array);
        
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