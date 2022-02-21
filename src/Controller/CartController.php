<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{


    /**
     * @Route("/cart/add/{id}", name="cart_add",requirements={"id":"\d+"})
     */
    public function add(CartService $cartService, $id, ProductRepository $productRepository, Request $request)
    {
        //0. Securisation : Verifions si le produit existe dans la BD
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit ' . $id . ' n\'existe pas');
        }

        $cartService->add($id);


        $this->addFlash('success', 'Bravo,Le produit a ete bien ajoute au panier !');

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }
    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $details = $cartService->getDetailsCartItems();
        $total = $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $details,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}",name="cart_delete",requirements={"id":"\d+"})
     */
    public function delete($id, ProductRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit ' . $id . ' n\'existe peut pas et ne peut pas etre supprime !');
        }

        $cartService->remove($id);

        $this->addFlash("success", "Le produit a bien ete supprime du Panier !");

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}",name="cart_decrement",requirements={"id":"\d+"})
     */
    public function decrement($id, CartService $cartService, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit ' . $id . ' n\'existe peut pas et ne peut pas  etre supprime !');
        }
        $cartService->decrement($id);
        $this->addFlash("success", "Le produit a bien ete enleve !");
        return $this->redirectToRoute("cart_show");
    }
}
