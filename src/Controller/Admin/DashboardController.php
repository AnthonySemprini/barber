<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Prestation;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator){
        
    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(ProduitCrudController::class)
        ->generateUrl();
        
            return $this->redirect($url);
       
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Barber');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Client');
        
        yield MenuItem::section('Users');
        
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter user', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir user', 'fas fa-eye', User::class)
        ]);
        yield MenuItem::section('E-commerce');
        
        yield MenuItem::section('Produits');
        
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter produit', 'fas fa-plus', Produit::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir produit', 'fas fa-eye', Produit::class)
        ]);
        yield MenuItem::section('Categories');
        
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer Categorie', 'fas fa-plus', Categorie::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir produit', 'fas fa-eye', Categorie::class)
        ]);
        
        yield MenuItem::section('RDV');
        yield MenuItem::section('Prestations');
        
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer préstation', 'fas fa-plus', Prestation::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir préstation', 'fas fa-eye', Prestation::class)
        ]);
        
        yield MenuItem::section('Reservations');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer reservation', 'fas fa-plus', Reservation::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir reservation', 'fas fa-eye', Reservation::class)
        ]);


    }
}
