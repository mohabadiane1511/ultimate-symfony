<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }



    public function sendEmail(ProductViewEvent $productViewEvent)
    {

        /*  $email = new TemplatedEmail();
        $email->from(new Address('contact@mail.com', 'Infos de la boutique'))
            ->to("admin@mail.com")
            ->htmlTemplate('email/product_view.html.twig')
            ->context([
                'product' => $productViewEvent->getProduct()
            ])
            ->subject('Visite du produit n*' . $productViewEvent->getProduct()->getId());

        $this->mailer->send($email); */
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }
}
