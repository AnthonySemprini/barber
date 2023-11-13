<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
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

        $total = 0;

        foreach ($panierWithData as $article) {
            $totalArticle = $article['produit']->getPrix() * $article['quantite'];
            $total += $totalArticle;
        }
        $totalArticle = array_sum($panier);
              
        return $this->render('panier/index.html.twig', [
            'articles' => $panierWithData,
            'total' => $total,
            'totalArticle' => $totalArticle,
        ]);
    }
    #[Route('/panier/add/{id}', name:'app_panier_add')]
    public function add(int $id, SessionInterface $session){
        
       
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
    

    #[Route('/panier/remove/{id}', name:'app_panier_remove')]
    public function remove($id, SessionInterface $session) {

        //Recupére le panier de la session
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
        
        //Recupére le panier de la session
        $panier = $session->get('panier', []);
        
        //si produit est en panier augmente de 1
        if(!empty($panier[$id])) {
            $panier[$id]++;
        }
        $session->set('panier', $panier);
        
        //redirige vers le panier
        return $this->redirectToRoute('app_panier');
    }
    
    #[Route('/panier/downQtt/{id}', name:'app_panier_downQtt')]
    public function downQtt($id, SessionInterface $session) {
        
        //Recupére le panier de la session
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
    
    #[Route('/panier/empty', name:'app_panier_removeAll')]
    public function removeAll(SessionInterface $session) 
    {
        $session->remove('panier');
    
    
        return $this->redirectToRoute('app_panier');
    }
}
