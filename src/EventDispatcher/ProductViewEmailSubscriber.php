<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info('Email envoye a ladmin pour lafiichage de produit' . $productViewEvent->getProduct()->getId());
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }
}
