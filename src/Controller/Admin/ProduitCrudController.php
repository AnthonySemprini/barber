<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public const PRODUCTS_BASE_PATH = 'assets/img';
    public const PRODUCTS_UPLOAD_DIR = 'public/assets/img';

    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
        return [
            IdField::new('id')->hideOnform(),
            TextField::new('nom'),
            NumberField::new('prix')
                ->setLabel('prix')
                ->setFormType(NumberType::class)
                ->setFormTypeOptions([
                    'html5' => true, // Activer le support HTML5
                    'attr' => ['step' => 0.01], // Définir le nombre de décimales
            ]),
            TextEditorField::new('description'),
            ImageField::new('image')
                ->setBasePath(self::PRODUCTS_BASE_PATH)
                ->setUploadDir(self::PRODUCTS_UPLOAD_DIR)
                ->setSortable(false),
            AssociationField::new('Categorie')
        ];
    }
    
}
