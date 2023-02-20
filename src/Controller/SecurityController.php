<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifPassType;
use App\Form\EditProfilType;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{

    /**
     * Authentification de l'utilisateur
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà connecté(e).');
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Déconnexion
     *
     * @return Response
     */
    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        // Message flash
        $this->addFlash('danger', 'Vous étes bien déconnecté! A bientot.');
        //Rederiction vers la page de connexion
        return $this->redirectToRoute('app_login');
    }

    /**
     * Envoi d'email pour la récupération du mot de passe oublier.
     *
     * @param Request $request
     * @param UserRepository $usersRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntityManagerInterface $entityManager
     * @param SendMailService $mail
     * @return Response
     */
    #[Route('/pass-oubli', name: 'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());
            // On vérifie si on a un utilisateur
            if ($user) {
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // On crée les données du mail
                $context = compact('url', 'user');

                // Envoi du mail
                $mail->send(
                    'admin@fitnet.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'pass_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('emails/request_password_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Changement de mot de passe oublier
     *
     * @param string $token
     * @param Request $request
     * @param UserRepository $usersRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // On vérifie si on a ce token dans la base
        $user = $usersRepository->findOneByResetToken($token);

        // On vérifie si l'utilisateur existe

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // On efface le token
                $user->setResetToken('');

                // On enregistre le nouveau mot de passe en le hashant
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('emails/password_reset.html.twig', [
                'passForm' => $form->createView()
            ]);
        }

        // Si le token est invalide on redirige vers le login
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }

    /**
     * Changement du mot de passe ou données pérsonnel de l'utilisateur à partir du page profil
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/profil/parametre/{slug}{id}',  name: 'modif_pass', methods: ["GET","POST"])]

    public function modifPass(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // On s'assure qu'un utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous n\'êtes pas connecté(e)!');
            return $this->redirectToRoute('app_login');
        }
        
        // On s'assure que l'utilisateur connecté(e) modifier bien son propre compte
        if ($this->getUser() === $user) {
            // Formulaire du changement de mot de passe
            $formPassChange = $this->createForm(ModifPassType::class);
            $formPassChange->handleRequest($request);


            if ($formPassChange->isSubmitted() && $formPassChange->isValid()) {
                // get l'ancien mot de passe
                $oldPassword = $request->get('modif_pass')['actuelPassword'];
                // On vérifie l'ancien mot de passe
                if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                    // On enregistre le nouveau mot de passe en le hashant
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            $formPassChange->get('plainPassword')->getData()
                        )
                    );
                    // enregistrement du nouveau mot de passe dans la bese de données
                    $entityManager->flush();
                    // Confirmation du changement du mot de passe
                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('app_profil');
                }
            }

            // Formulaire du changement du profil de l'utilisateur
            $formProfilChange = $this->createForm(EditProfilType::class, $user);
            $formProfilChange->handleRequest($request);
            
            if ($formProfilChange->isSubmitted() && $formProfilChange->isValid()){
                $entityManager->persist($user);
                $entityManager->flush();
                // message de succee
                $this->addFlash('message', 'Profile à jour. Merci');
                // redirection vers le profile
                return $this->redirectToRoute('app_profil');
            }

            return $this->render('security/setting_change.html.twig', [
                'modifPassForm' => $formPassChange->createView(),
                'changeProfilForm' => $formProfilChange->createView(),
            ]);
        }
    }
}
