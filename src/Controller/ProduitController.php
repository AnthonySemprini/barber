<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $produitRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Utilisation de knpPaginator pour paginer les resultat de la requete produit
        $pagination = $paginator->paginate(
            $produitRepository->paginationQuery(),
            $request->query->get('page', 1),
            5 //nombres d'elements par page
        );

        //redirige vers produits avec la pagination
        return $this->render('produit/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    
    #[Route('/detail{id}', name: 'app_detail_produit')]
    public function detailProduit(Produit $produit): Response
    {
        //redirige vers la vue du detail d'un produit
        return $this->render('produit/detail.html.twig',[
            'produit' => $produit,
        ]
    );
    }
}
