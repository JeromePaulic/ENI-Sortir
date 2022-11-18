<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/sortie/{id}/inscription', name: 'inscription_inscriptionoupas')]
    public function inscriptionoupas(Sortie $sortie,
                                     InscriptionRepository $inscriptionRepository,
                                     EntityManagerInterface $entityManager
                                         ): Response
    {
        //vérification si il est déjà inscrit en bdd
        $verificationInscription=$inscriptionRepository->findOneBy(['participants'=>$this->getUser(),'sortie'=>$sortie]);
        // si déja inscrit je le désincrit
            if ($verificationInscription){
                $entityManager->remove($verificationInscription);
                $entityManager->flush();
                // todo  à faire avec la redicrection
                $this ->addFlash('success', 'oh non! tu es désinscrit(e)!');
                return $this ->redirectToRoute('sortie_details',['id'=>$sortie->getId()]);

            }

            //si non inscrit on l'incrit à la sortie
            //on vérifie si il reste des places
            if ($sortie->sortieComplete()){
                //todo redirection
                $this ->addFlash('error', "oh non! il n'y a plus de place!");
                return $this ->redirectToRoute('home',['id'=>$sortie->getId()]);
            }

            //si la date est > à la date de cloture


            // si ok alors on l'inscrtit à l'activité
            $inscription = new Inscription();
            $inscription->setParticipants($this->getUser());
            $inscription->setSortie($sortie);

            $entityManager->persist($inscription);
            $entityManager->flush();
            // todo message de sucssès vous etes inscrit + redirection
        $this ->addFlash('success', 'Vous etes inscrit(e)');
        return $this ->redirectToRoute('sortie_details',['id'=>$sortie->getId()]);






    }
}
