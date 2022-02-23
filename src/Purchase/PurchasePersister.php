<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use DateTime;
use App\Entity\PurchaseItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security, $cartService, $manager;

    public function __construct(Security $security, CartService $cartService, ManagerRegistry $manager)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->manager = $manager;
    }
    public function storePurchase(Purchase $purchase)
    {
        $user = $this->security->getUser();
        $purchase->setUser($user)
            ->setPurchaseAt(new DateTime())
            ->setTotal($this->cartService->getTotal());
        $this->manager->getManager()->persist($purchase);


        foreach ($this->cartService->getDetailsCartItems() as $cartItems) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPruchase($purchase)
                ->setProduct($cartItems->product)
                ->setProductName($cartItems->product->getName())
                ->setQuantity($cartItems->qty)
                ->setTotal($cartItems->getTotal())
                ->setProductPrice($cartItems->product->getPrice());


            $this->manager->getManager()->persist($purchaseItem);
        }

        //Enregistrer la commande
        $this->manager->getManager()->flush();
    }
}
