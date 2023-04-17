<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // !!!!! INGREDIENTS !!!!!

        $ingredients = [];
        
        for ($i=1; $i <= 50; $i++) 
        { 
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word(1, true));  // 1 --> Nombre de mots générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
            $ingredient->setPrice(mt_rand(0, 100));

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // !!!!! RECIPES !!!!!
        
        for ($j=1; $j <= 20 ; $j++) 
        { 
            $recipe = new Recipe();
            $recipe->setName($this->faker->word(2, true));  // 2 --> Nombre de mots générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
            $recipe->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null);
            $recipe->setnbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null);
            $recipe->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null);
            $recipe->setDescription($this->faker->text(200)); // 200 --> Nombre de mots générés
            $recipe->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null);
            $recipe->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);

            for ($k=0; $k < mt_rand(5, 15); $k++) 
            { 
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients)-1)]);
            }
            
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
