<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnform(), //masque l'ID
            TextField::new('pseudo'),
            // TextField::new('password'),
            Field::new( 'password', 'New password' )->onlyWhenCreating()->setRequired( true )
                   ->setFormType( RepeatedType::class )
                   ->setFormTypeOptions( [
                       'type'            => PasswordType::class,
                       'first_options'   => [ 'label' => 'Password' ],
                       'second_options'  => [ 'label' => 'Confirmation password' ],
                       'error_bubbling'  => true,
                       'invalid_message' => 'The password fields do not match.',
                   ] ),
            EmailField::new('email'),
        ];
    }
    
}
