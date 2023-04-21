<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification du profil 
    // ET SEULEMENT l'utilisateur à qui "appartient" ce profil
    /**
     * This function allow us to edit user's profil
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) 
            {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                // !!!!! Message flash : Modification profil utilisateur !!!!!
                $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
                (
                    'success',  // Nom de l'alerte 
                    ['info' => 'Modification !','bonus' => "Le compte utilsateur M./Mme \"" . $user->getFullName() . "\" a bien été modifié"]  // Message(s)
                );

                return $this->redirectToRoute('recipe.index');
            }
            else
            {
                // !!!!! Message flash : Modification profil utilisateur !!!!!
                $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
                (
                    'danger',  // Nom de l'alerte 
                    ['info' => 'Erreur !','bonus' => "Le mot de passe renseigné est incorrect"]  // Message(s)
                );
            }  
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification du mot de passe
    // ET SEULEMENT l'utilisateur à qui "appartient" ce mot de passe
    /**
     * This function allow us to modify the user's password
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function editPassword(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {
        // REDIRECTION SANS UTILISER "Security"
        // if (!$this->getUser()) 
        // {
        //     return $this->redirectToRoute('security.login');
        // }

        // if ($this->getUser() !== $user)
        // {
        //     return $this->redirectToRoute('recipe.index');
        // }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) 
            {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable()); // Nécessaire de modifier cette colonne pour que le 'plainPassword' se mette à jour
                                                               // Car le 'plainPassword' n'est pas une colonne

                // $user->setPassword($hasher->hashPassword($user, $form->getData()['newPassword']));
                $choosenUser->setPlainPassword($form->getData()['newPassword']); // Necessite de mettre à jour une colonne de la table pour se mettre à jour
                                                                          // Ici, on met à jour la colonne 'UpdatedAt'

                $manager->persist($choosenUser);
                $manager->flush();

                // !!!!! Message flash : Modification mot de passe utilisateur !!!!!
                $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
                (
                    'success',  // Nom de l'alerte 
                    ['info' => 'Modification !','bonus' => "Le mot de passe de M./Mme \"" . $user->getFullName() . "\" a bien été modifié"]  // Message(s)
                );

                return $this->redirectToRoute('recipe.index');
            }
            else
            {
                // !!!!! Message flash : Modification profil utilisateur !!!!!
                $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
                (
                    'danger',  // Nom de l'alerte 
                    ['info' => 'Erreur !','bonus' => "Le mot de passe renseigné est incorrect"]  // Message(s)
                );
            }  
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
