<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
 

#[Route('/produit', name: 'app_produit')]
public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, Request $request, PaginatorInterface $paginator): Response
{
    $category = $request->query->get('category'); // Récupère la catégorie du paramètre de requête

    // Choisissez la bonne requête en fonction de la catégorie
    if ($category) {
        $query = $produitRepository->findByCategory($category);
    } else {
        $query = $produitRepository->paginationQuery();
    }

    $pagination = $paginator->paginate(
        $query,
        $request->query->get('page', 1),
        5 // Nombres d'éléments par page
    );

    $categories = $categorieRepository->findAll();

    return $this->render('produit/index.html.twig', [
        'pagination' => $pagination,
        'currentCategory' => $category,
        'categories' => $categories
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
