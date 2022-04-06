<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email'),
            TextField::new('password'),
            TextField::new('imageFile', 'Upload')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('imageName', 'Fichier')->setBasePath('/fichier')->hideOnForm(),
            AssociationField::new('demandes'),
            ArrayField::new('roles')
        ];

       /* yield EmailField::new('email');
        yield TextField::new('password');

        yield TextField::new('imageFile', 'Upload')
            ->setFormType(VichImageType::class)
           ->onlyOnForms();

        yield ImageField::new('imageName', 'Fichier')
                ->setBasePath('/fichier')
                ->hideOnForm();

        yield AssociationField::new('demandes');
        yield ArrayField::new('roles');*/
    }

}
