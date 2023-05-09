<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testIfSubmitContactFormIsSuccessful(): void
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact'); // On teste que le H1 contenant "Formulaire de contact" existe
        
        
        // RECUPERER LE FORMULAIRE
        
        $submitButton = $crawler->selectButton('Valider');
        //dd($submitButton);
        
        $form = $submitButton->form();
        
        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@monmail.com";
        $form["contact[subject]"] = "Subject Test";
        $form["contact[message]"] = "Message Test";
        
        // SOUMETTRE LE FORMULAIRE
        

        $client->submit($form);
        // dd($test);
        
        // VERIFIER LE STATUT HTTP
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);  // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK
        
        // VERIFIER L'ENVOI DU MAIL
        
        $this->assertEmailCount(1);
        
        $client->followRedirect();

        // // VERIFIER LA PRESENCE DU MESSAGE DE SUCCES

        $this->assertSelectorTextContains(
            'div.alert.alert-success.mt-4',
            'Demande de contact ! Votre message a bien été envoyé à notre équipe'
        );
        
    }
}
