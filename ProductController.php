<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ProductController extends AbstractDashboardController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        {
            if ($this->isGranted('ROLE_USER')) {
                return $this->render('Admin/dashboard.html.twig');
            } else
                return $this->redirectToRoute('app_homepage');
        }
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CaroleYou');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Go To Site', 'fa-solid fa-arrow-rotate-left', 'app_homepage');

        //DEFINITION DU ROLE ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-Post')
                ->setPermission('ROLE_ADMIN');
        }

        //DEFINITION DU ROLE EDITOR
        if ($this->isGranted('ROLE_EDITOR')) {
            yield MenuItem::section('Post');
            yield MenuItem::subMenu('Post', 'fa-sharp fa-solid fa-blog')
                ->setSubItems([
                    MenuItem::linkToCrud('Create Post', 'fas fa-newspaper', Product::class)->setAction(Crud::PAGE_NEW),
                    MenuItem::linkToCrud('Show Post', 'fas fa-eye', Product::class),
                ]);
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // autres champs...
            ImageField::new('image')->setBasePath('divers/images')->setLabel('Image')->onlyOnIndex(),
            ImageField::new('image')->setBasePath('divers/images')->setLabel('Image')->onlyOnDetail(),
        ];
    }
}
