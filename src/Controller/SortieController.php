<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
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
        $sortie = new Sortie();
        $sortie ->setOrganisateur($this->getUser());

        $sortieForm = $this-> createForm(SortieType::class,$sortie);

        $sortieForm->handleRequest($request);
            if($sortieForm->isSubmitted() && $sortieForm->isValid()){
                //todo géré l'état de la sortie peu-être avec un service?

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this ->addFlash('success', 'Sortie crée!');
                return $this ->redirectToRoute('sortie_details',['id'=>$sortie->getId()]);

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

            return $this ->redirectToRoute('');
        }

        #[Route('/sortie/details/{id}', name: 'sortie_details')]
            public function details (int $id, SortieRepository $sortieRepository): Response{
            $sortie= $sortieRepository->find($id);

            return $this->render('sortie/detail.html.twig', [
                        "sortie"=>$sortie
            ]);
        }

        #[Route('/sortie/list', name: 'sortie_list')]
        public function list (SortieRepository $sortieRepository): Response{
                $sorties = $sortieRepository->findAll();

            return $this->render('sortie/list.html.twig', [
                        "sorties"=>$sorties
            ]);
        }



}


