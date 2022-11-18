<?php

namespace App\Controller\Admin;

use App\Entity\JobPosition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JobPositionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JobPosition::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('company'),
            DateField::new('startDate'),
            DateField::new('endDate'),
            TextField::new('fromOfEmployment'),
            TextField::new('description'),
            TextField::new('individual', 'User'),
            BooleanField::new('active')->renderAsSwitch(false),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('individual')
            ->add('company')
            ->add('formOfEmployment')
            ->add('active');
    }


    public function configureActions(Actions $actions): Actions
    {
        return Actions::new();
    }

}
