<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository, PrestationRepository $prestationRepository):Response
    {
    
        $prestations = $prestationRepository->findWithStar();

        
        $produits = $produitRepository->findLastThreeProd();
        
        //dd($prestations);

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'prestations' => $prestations,
                'produits' => $produits,

            ]);
    }

 
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        //redirige page profil user
        return $this->render('home/profil.html.twig');
    }
    
}
