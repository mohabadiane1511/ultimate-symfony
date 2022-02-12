<?php

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HelloController
{
    /**
     * @Route("/hello/{prenom?World}",name="hello")
     */

    public function hello($prenom = 'World', Environment $twig)
    {
        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,

        ]);
        return new Response($html);
    }
}
