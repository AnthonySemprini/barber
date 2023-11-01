<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitCommandeController extends AbstractController
{
    #[Route('/produit/commande', name: 'app_produit_commande')]
    public function index(): Response
    {
        return $this->render('produit_commande/index.html.twig', [
            'controller_name' => 'ProduitCommandeController',
        ]);
    }
        public function addProduit(Produit $produit)
    {
        $this->produits[] = $produit;

        return $this;
    }
}
