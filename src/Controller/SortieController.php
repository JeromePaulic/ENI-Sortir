<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/cree', name: 'sortie_cree')]
    public function cree(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie=new Sortie();
        $sortieForm = $this-> createForm(SortieType::class,$sortie);

        $sortieForm->handleRequest($request);
            if($sortieForm->isSubmitted() && $sortieForm->isValid()){
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

        return $this->render('sortie/cree.html.twig', [
            'sortieForm'=> $sortieForm->createView()
        ]);
    }
        #[Route('/sortie/surprimer/{id}', name: 'sortie_supprimer')]
        public function supprimer (Sortie $sortie, EntityManagerInterface $entityManager)
        {
            $entityManager-> remove($sortie);
            $entityManager->flush();

            return $this ->redirectToRoute();
        }

}
