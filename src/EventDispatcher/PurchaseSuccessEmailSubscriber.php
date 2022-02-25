<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{

    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(
        LoggerInterface $logger,
        Security $security,
        MailerInterface $mailer
    ) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        //Recupere Le user en lignee
        /** @var User */
        $currentuser = $this->security->getUser();

        //Recuperer la commande
        $purchase = $purchaseSuccessEvent->getPurchase();

        //Ecrire le mail
        $email = new TemplatedEmail();
        $email->from('contact@mohamedbadiane.com')
            ->to(new Address($currentuser->getEmail(), $currentuser->getFullName()))
            ->htmlTemplate('email/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentuser
            ])
            ->subject('Bravo,votre commande n* ' . $purchase->getId() . ' a ete bien enregitre');

        $this->mailer->send($email);

        //Envoi l'email

        $this->logger->info('Email envoye pour la commande n*' . $purchaseSuccessEvent->getPurchase()->getId());
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
}
