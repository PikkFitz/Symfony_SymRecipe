<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    /**
     * This function display all ingredients
     * 
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /* Nombre de page */
            10 /* Limite d'éléments par page */
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    
    #[Route('ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    /**
     * This function show a form to create an ingredient (add an ingredient to the list)
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $ingredient = $form->getData();
            // dd($ingredient);

            $manager->persist($ingredient);
            $manager->flush();

            // !!!!! Message flash : Ajout ingrédient !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'success',  // Nom de l'alerte 
                ['info' => 'Ajout !','bonus' => "L'ingrédient \"" . $ingredient->getName() . "\" a bien été ajouté"]  // Message(s)
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }
   

    #[Route('ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    /**
     * This function show a form to edit an ingredient when click on the "Modifier" button
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        // On récupère l'ingrédient (en paramètre de la fonction edit()), afin de récupérer son id)

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $ingredient = $form->getData();
            // dd($ingredient);

            $manager->persist($ingredient);
            $manager->flush();

            // !!!!! Message flash : Modification ingrédient !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'warning',  // Nom de l'alerte 
                ['info' => 'Modification !','bonus' => "L'ingrédient \"" . $ingredient->getName() . "\" a bien été modifié"]  // Message(s)
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

   
    #[Route('ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    /**
     * This function delete the selected ingredient when click on the "Supprimer" button
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        // !!!!! Message flash : Suppression ingrédient !!!!!
        $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
        (
            'danger',  // Nom de l'alerte 
            ['info' => 'Suppression !','bonus' => "L'ingrédient \"" . $ingredient->getName() . "\" a bien été supprimé"]  // Message(s)
        );
        

        return $this->redirectToRoute('app_ingredient');
    }
}




?>
