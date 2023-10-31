<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository,PrestationRepository $prestationRepository): Response
    {
        $produits = $produitRepository->findAll();
        $prestations = $prestationRepository->findAll();
        //redirige homePage
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits,
            'prestations' => $prestations,
    
        ]);
    }
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        //redirige page profil user
        return $this->render('home/profil.html.twig');
    }
    
}
