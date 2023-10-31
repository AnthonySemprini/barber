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
    public function findPrestaStar(ProduitRepository $produitRepository, PrestationRepository $prestationRepository, EntityManagerInterface $em):Response
    {
        $dql = "SELECT pr FROM App\Entity\Produit pr ORDER BY pr.id DESC";
        $query = $em->createQuery($dql)
            ->setMaxResults(3);
   
        $produits = $query->getResult();

        
        $dql = "SELECT p FROM App\Entity\Prestation p WHERE p.Star = 1";
        $query = $em->createQuery($dql)->setMaxResults(3);
        $prestations = $query->getResult();

            $prestations = $query->getResult();

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
