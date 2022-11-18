<?php

namespace App\Controller\Admin;

use App\Entity\FormOfEmployment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FormOfEmploymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormOfEmployment::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')
        ];

    }
}
