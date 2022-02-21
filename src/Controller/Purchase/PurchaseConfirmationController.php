<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
    /**
     * @Route("/purchase/confirm",name="purchase_confirm")
     * @IsGranted("ROLE_USER",message="Vous devez vous connecter pour enregistrer  une commande")
     */

    public function confirm(
        Request $request,

        RouterInterface $router,

        CartService $cartService,
        ManagerRegistry $manager
    ) {
        // Lire les donnees du form
        //FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash("warning", "Vous devez remplir le formulaire");
            return $this->redirectToRoute('cart_show');
        }
        //Si le formulaire na pas ete soumis : degager
        //Si pas connecte : degager
        $user = $this->getUser();

        //Si pas de produit : degager
        $cartItems = $cartService->getDetailsCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash("warning", 'Vous ne pourvez pas confirmer une commande avec u  panier vide');
            return $this->redirectToRoute("cart_show");
        }
        //Creer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $purchase->setUser($user)
            ->setPurchaseAt(new DateTime())
            ->setTotal($cartService->getTotal());
        $manager->getManager()->persist($purchase);


        foreach ($cartService->getDetailsCartItems() as $cartItems) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPruchase($purchase)
                ->setProduct($cartItems->product)
                ->setProductName($cartItems->product->getName())
                ->setQuantity($cartItems->qty)
                ->setTotal($cartItems->getTotal())
                ->setProductPrice($cartItems->product->getPrice());


            $manager->getManager()->persist($purchaseItem);
        }


        //Vider le panier avant denregistrer dasn la BD

        $cartService->empty();
        //Enregistrer la commande
        $manager->getManager()->flush();

        $this->addFlash('success', 'La commande a bien ete enregistre');

        return $this->redirectToRoute('purchase_index');
    }
}
