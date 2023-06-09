<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    public function getEntity() : Recipe
    {
        return (new Recipe())->setName('Recipe #1')
            ->setDescription('Description #1')
            ->setIsFavorite(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }

    // TEST ENTITE RECETTE INVALIDE
    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();

        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(0, $errors);  // Pour que le test soit valide uniquement si il y a n erreurs (ici 0)
    }

    // TEST NOM DE RECETTE INVALIDE
    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();


        $recipe = $this->getEntity();
        $recipe->setName('');

        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(2, $errors);  // Pour que le test soit valide uniquement si il y a n erreurs (ici 2, car on s'attend à 2 erreurs
                                         // puisque dans Recipe.php, nous lui demandons d'être NotBlank et d'avoir une taille min = 2)
    }

    // TEST MOYENNE D'UNE NOTE
    public function testGetAverage()
    {
        $recipe = $this->getEntity();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1); // User id =1

        for ($i=0; $i < 5; $i++)
        { 
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);

            $recipe->addMark($mark);
        }

        $this->assertTrue(2.0 === $recipe->getAverage());  // 2.0 car Symfony attend un float
        
    }

}
