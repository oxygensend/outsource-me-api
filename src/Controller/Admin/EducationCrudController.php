<?php

namespace App\Controller\Admin;

use App\Entity\Education;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EducationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Education::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('university'),
            DateField::new('startDate'),
            DateField::new('endDate'),
            TextField::new('fieldOfStudy'),
            TextField::new('title'),
            NumberField::new('grade'),
            TextField::new('description'),
            TextField::new('individual', 'User')
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('individual')
            ->add('university')
            ->add('title');
    }


    public function configureActions(Actions $actions): Actions
    {
        return Actions::new();
    }


}
