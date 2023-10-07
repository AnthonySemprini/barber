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
        //Recupére le pannier de la session
        $panier = $session->get('panier', []);

        $panierWithData = [];
        foreach ($panier as $id => $quantite) {
            //Obtienir pour chaque élément du panier les details du produit
            $panierWithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantite
            ];
        }
        //dd($panierWithData);

        $total = 0;

        foreach ($panierWithData as $article) {
            //multiplie le nombres de produits par leur prix pour avoir le total prix par produit  
            $totalArticle = $article['produit']->getPrix() * $article['quantite'];
            $total += $totalArticle;
        }
        //redirige vers le panier
        return $this->render('panier/index.html.twig', [
            'articles' => $panierWithData,
            'total' => $total
        ]);
    }
    #[Route('/panier/add/{id}', name:'app_panier_add')]
    public function add($id, SessionInterface $session ){
        
        $panier = $session->get('panier', []);

        //verifie si le produit et deja en panier si oui 1 sinon ajoute le produit au panier
        if(!empty($panier[$id])) {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        //redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }
        //dd($session->get('panier'));
    

    #[Route('/panier/remove/{id}', name:'app_panier_remove')]
    public function remove($id, SessionInterface $session) {

        //Recupére le pannier de la session
        $panier = $session->get('panier', []);

        //verifie si le produit et en panier si oui le supprimer
        if(!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/upQtt/{id}', name:'app_panier_upQtt')]
    public function upQtt($id, SessionInterface $session) {
       
        //Recupére le pannier de la session
        $panier = $session->get('panier', []);

        //si produit est en pannier augmente de 1
        if(!empty($panier[$id])) {
            $panier[$id]++;
        }
        $session->set('panier', $panier);

        //redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/downQtt/{id}', name:'app_panier_downQtt')]
    public function downQtt($id, SessionInterface $session) {
        
        //Recupére le pannier de la session
        $panier = $session->get('panier', []);

         //si produit et egal a 1 le supprimer sinon le diminuer de 1
        if(($panier[$id])== 1) {
            unset($panier[$id]);
        }else{
            $panier[$id]--;
        }
        $session->set('panier', $panier);

        //redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }
}
