<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    /**
     * This function display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /* Nombre de page */
            10 /* Limite d'éléments par page */
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    /**
     * This function show a form to create a recipe (add a recipe to the list)
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            // !!!!! Message flash : Ajout recette !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'success',  // Nom de l'alerte 
                ['info' => 'Ajout !','bonus' => "La recette \"" . $recipe->getName() . "\" a bien été ajoutée"]  // Message(s)
            );

            return $this->redirectToRoute('recipe.index');
        }


        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    /**
     * This function show a form to edit a recipe when click on the "Modifier" button
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        // On récupère la recette (en paramètre de la fonction edit()), afin de récupérer son id)

        $form = $this->createForm(RecipeType::class, $recipe);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $recipe = $form->getData();
            // dd($recipe);

            $manager->persist($recipe);
            $manager->flush();

            // !!!!! Message flash : Modification recette !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'warning',  // Nom de l'alerte 
                ['info' => 'Modification !','bonus' => "La recette \"" . $recipe->getName() . "\" a bien été modifiée"]  // Message(s)
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    /**
     * This function delete the selected recipe when click on the "Supprimer" button
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();

        // !!!!! Message flash : Suppression recette !!!!!
        $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
        (
            'danger',  // Nom de l'alerte 
            ['info' => 'Suppression !','bonus' => "La recette \"" . $recipe->getName() . "\" a bien été supprimée"]  // Message(s)
        );
        
        return $this->redirectToRoute('recipe.index');
    }
}
