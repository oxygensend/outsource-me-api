<?php

namespace App\Controller\Admin;

use App\Entity\JobOffer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JobOfferCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JobOffer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('description'),
            TextField::new('address'),
            DateField::new('validTo'),
            TextField::new('fromOfEmployment'),
            TextField::new('description'),
            TextField::new('user', 'User'),
            TextField::new('experience'),
            IntegerField::new('displayOrder'),
            IntegerField::new('popularityOrder'),
            IntegerField::new('redirectCount', 'Redirects'),
            BooleanField::new('archived')->renderAsSwitch(false),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('address')
            ->add('workType')
            ->add('formOfEmployment')
            ->add('user')
            ->add('experience')
            ->add('archived');
    }


    public function configureActions(Actions $actions): Actions
    {
        return Actions::new();

    }

}