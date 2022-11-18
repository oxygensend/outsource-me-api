<?php

namespace App\Controller\Admin;

use App\Entity\AboutUs;
use App\Entity\Address;
use App\Entity\Application;
use App\Entity\Company;
use App\Entity\Education;
use App\Entity\FormOfEmployment;
use App\Entity\JobOffer;
use App\Entity\JobPosition;
use App\Entity\Language;
use App\Entity\Notification;
use App\Entity\Opinion;
use App\Entity\Technology;
use App\Entity\University;
use App\Entity\User;
use App\Entity\WorkType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Outsource Me');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Management');
        yield MenuItem::linkToCrud('About us', 'fa fa-star', AboutUs::class);
        yield MenuItem::linkToCrud('Push Notifications', 'fa fa-bell', Notification::class);

        yield MenuItem::section('Users')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Admin', 'fa fa-users', User::class)
            ->setController(AdminCrudController::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Editor', 'fa fa-users', User::class)
            ->setController(EditorCrudController::class)
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Personal data')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Education', 'fa fa-school', Education::class);
        yield MenuItem::linkToCrud('Application', 'fa fa-message', Application::class);
        yield MenuItem::linkToCrud('JobPositions', 'fa fa-person-digging', JobPosition::class);
        yield MenuItem::linkToCrud('JobOffers', 'fa fa-briefcase', JobOffer::class);
        yield MenuItem::linkToCrud('Opinions', 'fa fa-font-awesome', Opinion::class);

        yield MenuItem::section('Meta data');

        yield MenuItem::linkToCrud('Companies', 'fa fa-building', Company::class);
        yield MenuItem::linkToCrud('Universities', 'fa  fa-building-columns', University::class);
        yield MenuItem::linkToCrud('Addresses', 'fa  fa-location-arrow', Address::class);
        yield MenuItem::linkToCrud('Languages', 'fa fa-language', Language::class);
        yield MenuItem::linkToCrud('Technologies', 'fa fa-microchip', Technology::class);
        yield MenuItem::linkToCrud('Form of Employments', 'fa fa-house-laptop',FormOfEmployment::class);
        yield MenuItem::linkToCrud('Work Types', 'fa fa-bars', WorkType::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
