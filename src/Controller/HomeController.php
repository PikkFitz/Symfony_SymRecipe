<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository): Response
    {
        // !!!!!  POUR LE CACHE  !!!!!
        $cache = new FilesystemAdapter(); 
        $data = $cache->get('recipes', function(ItemInterface $item) use($recipeRepository)  // recipes -> clé  | Si aucune valeur n'est donnée via la clé, 
                                                                                       // alors la fonction get() utilise la fonction ItemIterface pour collecter les données
        {
            $item->expiresAfter(15);  // Temps d'expiration du cache (ici, 15 secondes)
            return $recipeRepository->findPublicRecipe(3); // Récupère les 3 dernières recettes publiques
        });
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!


        return $this->render('pages/home.html.twig', [
            'recipes' => $data
            // Affiche les 3 dernières recettes publiques (car la fonction findPublicRecipe() est classée par date de création descendant)
        ]);
    }

}


?>