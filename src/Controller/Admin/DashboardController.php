<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')] // Autorise uniquement les personnes ayant le 'ROLE_ADMIN' (administrateurs du site)
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SymRecipe - Administration') // Titre de la page
            ->renderContentMaximized(); // Pour que le contenu couvre toute la largeur du navigateur
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'); //fa fa-... correspond à l'icône FontAwesome
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class); //fa fa-... correspond à l'icône FontAwesome
        yield MenuItem::linkToCrud('Demandes de contact', 'fas fa-envelope', Contact::class); //fa fa-... correspond à l'icône FontAwesome
    }
}
