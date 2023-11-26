<?php

namespace App\Controller\Admin;


use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
            AssociationField::new('produitCommandes')
            ->setCrudController(ProduitCommandeCrudController::class)
            ->formatValue(function ($value, $entity) {
                // Itérer sur chaque ProduitCommande et récupérer les produits
                $produits = $entity->getProduitCommandes();
                if (!$produits) {
                    return 'N/A';
                }

                return implode(', ', array_map(function ($produitCommande) {
                    $produit = $produitCommande->getProduit();
                    $nomProduit = $produit->getNom(); // Assurez-vous que getNom() est la méthode correcte
                    $prix = $produit->getPrix(); // Assurez-vous que getPrix() est la méthode correcte
                    $quantite = $produitCommande->getQuantite(); // Assurez-vous que getQuantite() est la méthode correcte
                    $total = $prix * $quantite;
                    return "$nomProduit (Quantité: $quantite, Prix: $prix €)";
                }, $produits->toArray()));
            }),
        ];


    }
    
}
