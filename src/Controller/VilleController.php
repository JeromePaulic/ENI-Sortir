<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'ville_afficher')]
    public function afficher(VilleRepository $villeRepository): Response
    {

        $villes =$villeRepository ->findAll();
        return $this->render('ville/afficher.html.twig', [
               "villes"=>$villes
        ]);
    }
}
