<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];
        foreach ($panier as $id => $quantite) {
            $panierWithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantite
            ];
        }
        //dd($panierWithData);

        $total = 0;

        foreach ($panierWithData as $article) {
            $totalArticle = $article['produit']->getPrix() * $article['quantite'];
            $total += $totalArticle;
        }
        return $this->render('panier/index.html.twig', [
            'articles' => $panierWithData,
            'total' => $total
        ]);
    }
    #[Route('/panier/add/{id}', name:'app_panier_add')]
    public function add($id, SessionInterface $session ){
        
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        //dd($session->get('panier'));
    }
}
