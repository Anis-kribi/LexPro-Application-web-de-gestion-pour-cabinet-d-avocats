<?php

namespace App\Controller\Backend\Biens;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypebienController extends AbstractController
{
    #[Route('/backend/biens/type', name: 'backend_biens_type')]
public function type(): Response
{
    return $this->render('backend/biens/type.html.twig');
}

#[Route('/backend/biens/categorie', name: 'backend_biens_categorie')]
public function categorie(): Response
{
    return $this->render('backend/biens/categorie.html.twig');
}

#[Route('/backend/biens/caracteristique', name: 'backend_biens_caracteristique')]
public function caracteristique(): Response
{
    return $this->render('backend/biens/caracteristique.html.twig');
}

}
