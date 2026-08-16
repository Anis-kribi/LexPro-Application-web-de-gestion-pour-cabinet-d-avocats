<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontendAccueilController extends AbstractController
{
    #[Route('/Frontend/accueil', name: 'app_frontend_accueil')]
    public function index(): Response
    {
        return $this->render('Frontend/accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('/Frontend/produits', name: 'produit_list')]
    public function produitList(): Response
    {
        return $this->render('Frontend/produits/list.html.twig');
    }
    #[Route('/Frontend/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('Frontend/Dashboard/dashboard.html.twig');
    }
    #[Route('/Frontend/charts', name: 'charts_menu')]
    public function charts(): Response
    {
        return $this->render('Frontend/charts/charts.html.twig');
    }
    #[Route('/Frontend/cards', name: 'bar_cards')]
    public function cards(): Response
    {
        return $this->render('Frontend/cards/cards.html.twig');
    }
    #[Route('/Frontend/button', name: 'bar_buttons')]
    public function buttons(): Response
    {
        return $this->render('Frontend/button/buttons.html.twig');
    }
    #[Route('/Frontend/colors', name: 'utilities_color')]
    public function utilitiesColor(): Response
    {
        return $this->render('Frontend/colors/colors.html.twig');
    }
    #[Route('/Frontend/border', name: 'utilities_border')]
    public function utilitiesBorder(): Response
    {
        return $this->render('Frontend/border/border.html.twig');
    }
    #[Route('/Frontend/animation', name: 'utilities_animation')]
    public function utilitiesAnimation(): Response
    {
        return $this->render('Frontend/animation/animation.html.twig');
    }
    #[Route('/Frontend/other', name: 'utilities_other')]
    public function utilitiesOther(): Response
    {
        return $this->render('Frontend/other/other.html.twig');
    }
    #[Route('/Frontend/blank', name: 'blank')]
    public function blank(): Response
    {
        return $this->render('Frontend/blank/blank.html.twig');
    }
    #[Route('/Frontend/404', name: '404')]
    public function error404(): Response
    {
        return $this->render('Frontend/404/404.html.twig');
    }
}
