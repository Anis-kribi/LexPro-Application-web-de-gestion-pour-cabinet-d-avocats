<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number', name: 'lucky_number')]
    public function test(): Response
    {
        $number = random_int(0, 100);

        return $this->render('Blog/index.html.twig', [
            'number' => $number,
        ]);
    }

    #[Route('/Blog')]
    public function blog(): Response
    {
        // You can replace this with your actual logic
        return new Response('This is the blog page');
    }

    #[Route('/Blog/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('Blog/index.html.twig', [
            'accueil' => 'accueil',
        ]);
    }
}
