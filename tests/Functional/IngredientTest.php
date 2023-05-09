<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        
        // RECUPERER URLGENERATOR

        $urlGenerator = $client->getContainer()->get('router');

        // RECUPERER ENTITY MANAGER

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1); // Utilisateur id =1 (ici l'admin)

        $client->loginUser($user); // Fonction pour connecter un utilisateur

        // SE RENDRE SUR LA PAGE DE CREATION D'UN INGREDIENT

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        // GERER LE FORMULAIRE

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient",
            'ingredient[price]' => floatval(11),
        ]);

        $client->submit($form);

        // GERER LA REDIRECTION

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK

        $client->followRedirect();

        // GERER L'ALERTBOX ET LA REDIRECTION

        // On vérifie le message
        $this->assertSelectorTextContains('div.alert-success', 'Ajout ! L\'ingrédient "Un ingrédient" a bien été ajouté');

        // On vérifie la route pour la redirection
        $this->assertRouteSame('ingredient.index');

    }


    public function testIfListIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        
        // RECUPERER URLGENERATOR

        $urlGenerator = $client->getContainer()->get('router');

        // RECUPERER ENTITY MANAGER

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1); // Utilisateur id =1 (ici l'admin)

        $client->loginUser($user); // Fonction pour connecter un utilisateur

        // SE RENDRE SUR LA PAGE DE CREATION D'UN INGREDIENT

        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        // VERIFICATION DE LA REPONSE

        $this->assertResponseIsSuccessful();

        // VERIFICATION DE LA ROUTE
        $this->assertRouteSame('ingredient.index');

    }


    public function testIfUpdateIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        
        // RECUPERER URLGENERATOR

        $urlGenerator = $client->getContainer()->get('router');

        // RECUPERER ENTITY MANAGER

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1); // Utilisateur id =1 (ici l'admin)

        $client->loginUser($user); // Fonction pour connecter un utilisateur

        // RECUPERER UN INGREDIENT DE NOTRE UTILISATEUR

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        // SE RENDRE SUR LA PAGE DE MODIFICATION D'UN INGREDIENT

        $crawler = $client->request(
            Request::METHOD_GET, 
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        // GERER LE FORMULAIRE

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient 2",
            'ingredient[price]' => floatval(22),
        ]);

        $client->submit($form);

        // GERER LA REDIRECTION

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK

        $client->followRedirect();

        // GERER L'ALERTBOX ET LA REDIRECTION

        // On vérifie le message
        $this->assertSelectorTextContains('div.alert-warning', 'Modification ! L\'ingrédient "Un ingrédient 2" a bien été modifié');

        // On vérifie la route pour la redirection
        $this->assertRouteSame('ingredient.index');

    }


    public function testIfDeleteIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        
        // RECUPERER URLGENERATOR

        $urlGenerator = $client->getContainer()->get('router');

        // RECUPERER ENTITY MANAGER

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1); // Utilisateur id =1 (ici l'admin)
        
        $client->loginUser($user); // Fonction pour connecter un utilisateur

        // RECUPERER UN INGREDIENT DE NOTRE UTILISATEUR

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        // SUPPRESSION D'UN INGREDIENT

        $crawler = $client->request(
            Request::METHOD_GET, 
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        // GERER LA REDIRECTION

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // CODE : 302 | MESSAGE : FOUND ==> Message de redirection OK

        $client->followRedirect();

        // GERER L'ALERTBOX ET LA REDIRECTION

        // On vérifie le message
        $this->assertSelectorTextContains('div.alert-danger', 'Suppression ! L\'ingrédient "'.$ingredient->getName().'" a bien été supprimé');

        // On vérifie la route pour la redirection
        $this->assertRouteSame('ingredient.index');

    }

}
