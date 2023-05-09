<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessful(): void
    {
        $client = static::createClient();
        
        // GET ROUTE BY URLGENERATOR

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // FORMULAIRE

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.com",
            "_password" => "password"
        ]); // ATTENTION : Bien mettre les underscores devant "_username" et "_password"
            // ET METTRE UN NAME AU FORMULAIRE DE SECURITE DANS LE FICHIER "login.html.twig" (name="login")

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK

        // REDIRECT + HOME

        $client->followRedirect();

        $this->assertRouteSame('home.index');

    }


    public function testIfLoginFailedWhenPasswordIsWrong()
    {

        $client = static::createClient();
        
        // GET ROUTE BY URLGENERATOR

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // FORMULAIRE

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.com",
            "_password" => "wrong_password" // On rentre volontairement un faux mdp
        ]); // ATTENTION : Bien mettre les underscores devant "_username" et "_password"
            // ET METTRE UN NAME AU FORMULAIRE DE SECURITE DANS LE FICHIER "login.html.twig" (name="login")

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK

        // REDIRECT + HOME

        $client->followRedirect();

        $this->assertRouteSame('security.login'); // Comme le mdp est faux nous restons sur la page security.login 
                                                  // Nous ne sommes pas redirigé sur home.index (quand le mdp est OK)


        // VERIFICATION DU MESSAGE D'ERREUR LORS D'UNE ERREUR D'AUTHENTIFICATION

        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials."); 

    }
}
