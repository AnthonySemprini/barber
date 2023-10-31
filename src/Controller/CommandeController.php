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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{
    #[Route('/commande/add', name: 'app_commande_add')]
    public function add(EntityManagerInterface $entityManager, Request $request,SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        // //dd($panier);
        // $commande = new Commande();
        
        // //dd($commande);
        // $form = $this->createForm(CommandeFormType::class, $commande);
        // $form ->handleRequest($request);

        // $entityManager->persist($commande);
        // $entityManager->flush();

        if($panier === []){
            //Le panier est vide, on retourne sur la homepage
            return $this->redirectToRoute('app_home');
        }
     
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

}