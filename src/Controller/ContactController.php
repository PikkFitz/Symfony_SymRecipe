<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request, EntityManagerInterface $manager, MailService $mailService): Response
    {
        $contact = new Contact();

        if ($this->getUser()) // Pour le pré-remplissage des champs "Nom" et "Email" si la personne qui envoie le message est un utilisateur déjà enregistré sur notre site
        {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }
        
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // dd($form->getData());

            $contact = $form->getData();
            // dd($contact);

            $manager->persist($contact);
            $manager->flush();

            // !!!!! EMAIL !!!!!

            $mailService->sendEmail(
                $contact->getEmail(),
                $contact->getSubject(),
                'emails/contact.html.twig',
                ['contact' => $contact]
            );

            // !!!!! Message flash : Message de contact envoyé !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'success',  // Nom de l'alerte 
                ['info' => 'Demande de contact !','bonus' => "Votre message a bien été envoyé à notre équipe"]  // Message(s)
            );

            return $this->redirectToRoute('contact.index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
