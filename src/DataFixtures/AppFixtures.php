<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Mark;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Contact;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        // !!!!! USERS !!!!

        $users = [];

        $admin = new User();
        $admin->setFullName('Administrateur SymRecipe')
            ->setPseudo(null)
            ->setEmail('admin@symrecipe.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');

        $users[] = $admin;

        $manager->persist($admin);


        for ($l=0; $l <= 30; $l++) 
        { 
            $user = new User();
            $user->setFullName($this->faker->name);
            $user->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null);
            $user->setEmail($this->faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPlainPassword('password');

            $users[] = $user;

            $manager->persist($user);
        }

        // !!!!! INGREDIENTS !!!!!

        $ingredients = [];
        
        for ($i=1; $i <= 50; $i++) 
        { 
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word(1, true));  // 1 --> Nombre de mots générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
            $ingredient->setPrice(mt_rand(0, 100));

            $ingredient->setUser($users[mt_rand(0, count($users) - 1)]);

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // !!!!! RECIPES !!!!!

        $recipes = [];
        
        for ($j=1; $j <= 20; $j++) 
        { 
            $recipe = new Recipe();
            $recipe->setName($this->faker->word(2, true));  // 2 --> Nombre de mots générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
            $recipe->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null);
            $recipe->setnbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null);
            $recipe->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null);
            $recipe->setDescription($this->faker->text(200)); // 200 --> Nombre de mots générés
            $recipe->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null);
            $recipe->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);
            $recipe->setIsPublic(mt_rand(0, 1) == 1 ? true : false);

            $recipe->setUser($users[mt_rand(0, count($users) - 1)]);

            for ($k=0; $k < mt_rand(5, 15); $k++) 
            { 
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients)-1)]);
            }
            
            $recipes[] = $recipe;

            $manager->persist($recipe);
        }

        // !!!!! MARKS !!!!!

        foreach ($recipes as $recipe) 
        {
            for ($i=0; $i < mt_rand(0,4); $i++) // Chaque recette aura entre 0 et 4 notes
            { 
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5)) // Note aléatoire comprise entre 1 et 5
                ->setUser($users[mt_rand(0, count($users)-1)]) // Attribution de la note par un Utilisateur aléatoire parmis tous les utilisateurs
                ->setRecipe($recipe);

                $manager->persist($mark);
            }
        }

        // !!!!! CONTACT !!!!!

        for ($i=0; $i < 5 ; $i++) // Pour avoir 5 demande de contact
        { 
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . ($i + 1))
                ->setMessage($this->faker->text());

            $manager->persist($contact);
        }



        $manager->flush();
    }
}
