<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Etat;
class InscriptionController extends AbstractController
{
    #[Route('/sortie/{id}/inscription', name: 'inscription_inscriptionoupas')]
    public function inscriptionoupas(Sortie $sortie,
                                     InscriptionRepository $inscriptionRepository,
                                     EntityManagerInterface $entityManager

                                         ): Response
    {
        $date= date('Y-m-d',time());

        // vérification si sortie bien ouverte
        if ($sortie->getEtat()->getLibelle() !== "Ouverte"){
            $this->addFlash("error", "Cette sortie n'est pas ouverte aux inscriptions !");
            return $this->redirectToRoute('sortie_details', ["id" => $sortie->getId()]);
        }

        //vérification si il est déjà inscrit en bdd
        $verificationInscription=$inscriptionRepository->findOneBy(['participants'=>$this->getUser(),'sortie'=>$sortie]);
        // si déja inscrit je le désincrit
            if ($verificationInscription){
                $entityManager->remove($verificationInscription);
                $entityManager->flush();

                $this ->addFlash('success', 'oh non! tu es désinscrit(e)!');
                return $this ->redirectToRoute('sortie_details',['id'=>$sortie->getId()]);

            }
     /*   //vérification si la date de cloture des inscription est passé

        if ($sortie->getDateLimiteInscription() >= $date) {
            $this ->addFlash("error","Désolé,mais les inscriptions sont fermées!");
            return $this->redirectToRoute('sortie_details', ["id" => $sortie->getId()]);
        }
*/
            //si non inscrit on l'incrit à la sortie
            //on vérifie si il reste des places
            if ($sortie->sortieComplete()){
                $this ->addFlash('error', "oh non! il n'y a plus de place!");
                return $this ->redirectToRoute('sortie_list');
            }

            // si ok alors on l'inscrtit à l'activité
            $inscription = new Inscription();
            $inscription->setParticipants($this->getUser());
            $inscription->setSortie($sortie);

            $entityManager->persist($inscription);
            $entityManager->flush();


        // on refrech pour avoir le bon nombre d'incrit et on verifie si il rest des places sinon fermer la sortie

      $entityManager-> refresh($sortie);

        $this ->addFlash('success', 'Vous etes inscrit(e)');
        return $this ->redirectToRoute('sortie_details',['id'=>$sortie->getId()]);





    }
}
