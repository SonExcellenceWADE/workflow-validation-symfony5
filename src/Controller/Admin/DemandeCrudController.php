<?php

namespace App\Controller\Admin;

use App\Entity\Demande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DemandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Demande::class;
    }


    public function configureFields(string $pageName): iterable
    {
       /* return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];*/

        yield TextField::new('name');
        //yield AssociationField::new('user');
        yield ArrayField::new('status');


      /* $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y') + 5),
                'widget' => 'single_text',
            ]);
        if (Crud::PAGE_EDIT === $pageName) {
                    yield $createdAt->setFormTypeOption('disabled', true);
               } else {
                    yield $createdAt;
                }*/
    }

}
