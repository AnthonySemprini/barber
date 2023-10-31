<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function findPrestaStar(PrestationRepository $prestationRepository, EntityManagerInterface $em):Response
    {
        $dql = "SELECT p FROM App\Entity\Prestation p WHERE p.Star = 1";
        $query = $em->createQuery($dql)->setMaxResults(3);
        $prestations = $query->getResult();

            $prestations = $query->getResult();

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'prestations' => $prestations,

            ]);
    }

    #[Route('/', name: 'app_home_prod')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        //redirige homePage
        return $this->render('home/index.html.twig', [
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
