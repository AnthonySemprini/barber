<?php

namespace App\Controller\Admin;


use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('adresse'),
            TextField::new('codePostal'),
            TextField::new('ville'),
            DateTimeField::new('dateCommande'),
            BooleanField::new('recupCommande'),
        //     AssociationField::new('produitCommandes', 'Produits Commandés')
        //     ->onlyOnForms() // ou onlyOnDetail() en fonction de l'utilisation
        //     ->formatValue(function ($value, $entity) {
        //         return implode(', ', $entity->getProduitCommandes()->map(function($produitCommande) {
        //             return $produitCommande->getProduit()->getNom();
        //         })->toArray());
        //     })
            
        ];

        // if ($pageName == Crud::PAGE_DETAIL) {
        //     $fields[] = ArrayField::new('produitCommandes')
        //         ->setTemplatePath('admin/commande_detail.html.twig');
        // }
    
        // return $fields;
    }
    
}
