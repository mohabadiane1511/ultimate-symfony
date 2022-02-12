<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="homepage")
     */
    public function homepage(ManagerRegistry $doctrine,)
    {
        $em = $doctrine->getManager();
        $pr = $doctrine->getRepository(Product::class);
        $product = $pr->findBy([], [], 3);

        // $em->remove($product);
        // $em->flush();
        // dd($product);

        return $this->render('home.html.twig', [
            'products' => $product,
        ]);
    }
}
