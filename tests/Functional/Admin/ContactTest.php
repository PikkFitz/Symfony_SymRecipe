<?php

namespace App\Tests\Functional\Admin;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testCrudIsHere(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => 1]); // Utilisateur ID = 1

        $client->loginUser($user);  // On connecte l'utilisateur ID 1 (ici l'administrateur, pour avoir accès au panneau d'administration)

        $client->request(Request::METHOD_GET, '/admin');  // On va sur la page avec la route /admin

        $this->assertResponseIsSuccessful();  // On confirme que la réponse redirige vers l'URL 

        $crawler = $client->clickLink('Demandes de contact');  // On suit le lien "Demandes de contact"

        $this->assertResponseIsSuccessful();  // On vérifie sa réponse

        $client->click($crawler->filter('.action-new')->Link());  // On vérifie le bouton de class "action-new"

        $this->assertResponseIsSuccessful();  // On vérifie sa réponse

        // $client->request(Request::METHOD_GET, '/admin'); 

        $client->click($crawler->filter('.action-edit')->Link()); // On récupère le bouton de class "action-edit"

        $this->assertResponseIsSuccessful(); // On vérifie sa réponse       
    }

}


?>