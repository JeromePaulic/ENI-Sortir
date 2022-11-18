<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     * @param Request $Request
     * @param $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function profil(Request $Request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $profilForm = $this->createForm(ProfilType::class, $user);


        $profilForm->handleRequest($Request);

        if ($profilForm->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        //todo traiter le formulaire

        return $this->render('profil/profil.html.twig', [
            'profilForm' => $profilForm->createView()
        ]);
    }





    /**
     * @Route("/editprofil", name="editprofil")
     * @param Request $Request
     * @param $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function editprofil(Request $Request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $profilForm = $this->createForm(ProfilType::class, $user);


        $profilForm->handleRequest($Request);

        if ($profilForm->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        //todo traiter le formulaire

        return $this->render('profil/editprofil.html.twig', [
            'profilForm' => $profilForm->createView()
        ]);
    }



}