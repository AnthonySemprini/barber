<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;



class PanierService 
{
    
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(int $id, PanierService $panierService){

        $panier = $this->session->get('panier', []);

        //verifie si le produit et deja en panier si oui 1 sinon ajoute le produit au panier
        if(!empty($panier[$id])) {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);

    }
    
} 