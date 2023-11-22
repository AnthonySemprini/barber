<?php

namespace App\Controller\Admin;

use App\Entity\ProduitCommande;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProduitCommande::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            NumberField::new('quantite'),
            TextField::new('produit'),
            // NumberField::new('commande'),
        ];
    }
    
}
