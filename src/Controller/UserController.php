<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    /**
     * This function allow us to edit user's profil
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) 
        {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user)
        {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) 
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
    /**
     * This function allow us to modify the user's password
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function editPassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {
        if (!$this->getUser()) 
        {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user)
        {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) 
            {
                $user->setUpdatedAt(new \DateTimeImmutable()); // Nécessaire de modifier cette colonne pour que le 'plainPassword' se mette à jour
                                                               // Car le 'plainPassword' n'est pas une colonne

                // $user->setPassword($hasher->hashPassword($user, $form->getData()['newPassword']));
                $user->setPlainPassword($form->getData()['newPassword']); // Necessite de mettre à jour une colonne de la table pour se mettre à jour
                                                                          // Ici, on met à jour la colonne 'UpdatedAt'

                $manager->persist($user);
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
