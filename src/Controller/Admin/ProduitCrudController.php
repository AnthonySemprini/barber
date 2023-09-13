<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnform(),
            TextField::new('nom'),
            MoneyField::new('prix')->setCurrency('EUR'),
            TextEditorField::new('description'),
            TextField::new('image'),
            ChoiceField::new('categorie')->allowMultipleChoices()
        ];
    }
    
}
