<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $sessionInterface;
    protected $productRepository;

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface)
    {
        $this->session = $sessionInterface;
        $this->productRepository = $productRepository;
    }
    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }

    public function add(int $id)
    {
        // 1- Retrouvez le panier dans la Session
        //2- Si il n'existe pas encore ,alors prendre un tableau vide
        $cart = $this->getCart();
        //3- Voir si le produit existe deja dans le tableau        
        //4- Augmenter la quantite
        //5- Sinon ajouter le produit avec la quantite 

        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        //6* Enregistrer le tableau MAJ dans la session
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }
            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    public function getDetailsCartItems(): array
    {
        $details = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $details[] = new CartItem($product, $qty);
        }
        return $details;
    }

    public function remove(int $id)
    {
        $cart = $this->getCart;
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }
        // Soit le produit est a 1 donc il faut le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        //Soit le produit > 1 donc il faudrea le decrementer de 1
        $cart[$id]--;
        $this->saveCart($cart);
    }
}
