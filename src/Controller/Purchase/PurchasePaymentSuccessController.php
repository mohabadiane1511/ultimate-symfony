<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{

    /**
     * @Route("/purchase/terminate/{id}",name="purchase_payment_success")
     * @IsGranted("ROLE_USER",message="Vous devez vous connecter pour valider  une commande")
     */
    public function success(
        $id,
        PurchaseRepository $purchaseRepository,
        ManagerRegistry $manager,
        CartService $cartService
    ) {
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || ($purchase && $purchase->getUser() !== $this->getUser())
            || ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash("warning", "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $manager->getManager()->flush();
        $cartService->empty();
        $this->addFlash('success', 'La commande a ete paye et confirme');
        return $this->redirectToRoute('purchase_index');
    }
}
