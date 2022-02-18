<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\FormView;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList()
    {
        // Aller Chercher les categories dans la base de donnees

        $category = $this->categoryRepository->findAll();
        // Renvoyer le rendu HTML sous la forme d'une Reponse 
        return $this->render('category/_menu.html.twig', [
            'category' => $category
        ]);
    }
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $sluggerInterface, ManagerRegistry $managerRegistry): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($sluggerInterface->slug($category->getName())));

            $em = $managerRegistry->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();
        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit",name="category_edit")
     */
    public function edit(Request $request, $id, CategoryRepository $categoryRepository, ManagerRegistry $managerRegistry, Security $security): Response
    {
        /* $user =  $security->getUser();
        if ($user === null) {
            return $this->redirectToRoute('security_login');
        } 

        if ($security->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit d\'acceder a cette ressource');
        } */
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas le droit d\'acceder a cette ressource');


        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException('Cette categorie n\'existe pas !');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();

        return $this->render(
            'category/edit.html.twig',
            [
                'formView' => $formView,
                'category' => $category
            ]
        );
    }
}
