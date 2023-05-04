<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->filter('.btn.btn-primary.btn-lg'); // On vérifie qu'il y ait bien le lien "Inscription"
        $this->assertEquals(1, count($button)); // Et que ce lien existe 1 fois

        $recipes = $crawler->filter('.recipes .card'); // On vérifie qu'il y ait bien une card (pour les recettes de la communauté)
        $this->assertEquals(3, count($recipes)); // Et que cette card existe 3 fois


        $this->assertSelectorTextContains('h1', 'Bienvenue sur SymRecipe !');
    }
}
