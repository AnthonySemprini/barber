<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findBy([],
            ['nom' => 'ASC']),
        ]);
    }
    #[Route('/detail{id}', name: 'app_detail_produit')]
    public function detailProduit(Produit $produit): Response
    {
        return $this->render('produit/detail.html.twig', compact('produit'));
    }
}
