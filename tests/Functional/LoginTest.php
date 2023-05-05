<?php

namespace App\Tests\Functional;

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

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND

        $client->followRedirect();

        $this->assertRouteSame('home.index');

        // REDIRECT + HOME





    }
}
