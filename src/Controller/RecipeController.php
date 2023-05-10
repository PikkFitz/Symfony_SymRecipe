<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés)
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
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), /* Nombre de page */
            10 /* Limite d'éléments par page */
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')] // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés)
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
            $recipe->setUser($this->getUser());

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


    #[Route('/recette/communaute', name: 'recipe.community', methods: ['GET'])]
    // Accès à tous les utilisateurs (connectés ou non)
    public function indexPublic(
        RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        // !!!!!  POUR LE CACHE  !!!!!
        $cache = new FilesystemAdapter(); 
        $data = $cache->get('recipes', function(ItemInterface $item) use($repository)  // recipes -> clé  | Si aucune valeur n'est donnée via la clé, 
                                                                                       // alors la fonction get() utilise la fonction ItemIterface pour collecter les données
        {
            $item->expiresAfter(15);  // Temps d'expiration du cache (ici, 15 secondes)
            return $repository->findPublicRecipe(null); // Récupère les recettes publiques
        });
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!

        $recipes = $paginator->paginate(
            $data, 
            // La fonction findPublicRecipe() à été créée dans RecipeRepository 
            // Et sert à trouver les recettes "publiques" et les classe par date de création décroissante
            $request->query->getInt('page', 1), /* Nombre de page */
            10
        );

        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes
        ]);
    }


    #[Route('/recette/{id}', name: 'recipe.show', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() == true || user === recipe.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification des recettes 
    // ET SI la recette est publique OU Si l'utilisateur est celui qui à créé le recette
    /**
     * This function allow us to see a recipe if this one is public
     *
     * @param Recipe $recipe
     * @return Response
     */
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {
        $mark = new Mark();

        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // dd($form->getData());
            
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);

            if (!$existingMark) 
            {
                $manager->persist($mark);
                // dd($existingMark);
            }
            else 
            {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
                // dd($form->getData()->getMark());
            }

            $manager->flush();

            // !!!!! Message flash : Ajout note pour recette !!!!!
            $this->addFlash    // Nécessite un block "for message" dans index.html.twig pour fonctionner
            (
                'success',  // Nom de l'alerte 
                ['info' => 'Notation !','bonus' => "Votre note pour la recette \"" . $recipe->getName() . "\" a bien été prise en compte"]  // Message(s)
            );

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);

        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }


    #[Route('recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification des recettes 
    // ET SEULEMENT l'utilisateur à qui "appartiennent" ces recettes
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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de suppresion d'une recette 
    // ET SEULEMENT l'utilisateur à qui "appartient" cette recette
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
