<?php

namespace App\Controller;

use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */

    public function profil()
    {
        $user = new Profil();
        $profilForm = $this->createForm(ProfilType::class, $user);

        //todo traiter le formulaire

        return $this->render('profil/profil.html.twig', [
            'profilForm' => $profilForm->createView()
        ]);
    }
}