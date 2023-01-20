<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Groupe;
use App\Entity\Evenement;
use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Entity\TypeMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdmiDashboardController extends AbstractDashboardController
{
    /**
     * Panel adim avec les differentes categories
     *
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();
        return $this->render('admin/dashboard.html.twig');
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
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $templateVars = [];
        return Dashboard::new()
            ->setTitle('Tableau de bord Fitnet',$templateVars);
    }

    /**
     * Menu du panel Admin
     *
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Publications', 'fas fa-book', Publication::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comments', Commentaire::class);
        yield MenuItem::linkToCrud('Evenements', 'fas fa-calendar', Evenement::class);
        yield MenuItem::linkToCrud('Media', 'fas fa-photo-film', Media::class);
        yield MenuItem::linkToCrud('Groupes', 'fas fa-people-group', Groupe::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-tags', Tag::class);
        yield MenuItem::linkToCrud('TypesMessage', 'fas fa-message', TypeMessage::class);

    }

    /**
     * Undocumented function
     *
     * @param UserInterface $user
     * @return UserMenu
     
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenuItems = [
            MenuItem::linkToUrl('Profile','fa-id-card','/admin/profile'),
            MenuItem::linkToUrl('Settings','fa-user-cog','/admin/settings'),
            MenuItem::linkToLogout('__ea__user.sign_out', 'fa-sign-out')
        ];

        if ($this->isGranted(Permission::EA_EXIT_IMPERSONATION)) {
            $userMenuItems[] = 
            MenuItem::linkToExitImpersonation(
                '__ea__user.exit_impersonation', 
                'fa-user-lock'
            );
        }

        return UserMenu::new()
            ->displayUserName()
            ->displayUserAvatar()
            ->setName(method_exists($user, '__toString') ? (string) $user : $user->getEmail() )
            ->setAvatarUrl(null)
            ->setMenuItems($userMenuItems);
    }*/
}

