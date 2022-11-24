<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieRechercheType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\True_;
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
        public function supprimer (Sortie $sortie, EntityManagerInterface $entityManager,EtatRepository $etatRepository)
        {
            //chamgement de statut de la sortie en annulée
        $newEtat =$etatRepository->findOneBy(['libelle'=>'Annulée']);
        $sortie->setEtat($newEtat);



            $entityManager-> remove($sortie);
            $entityManager->flush();






            $this ->addFlash('success', "oh non! votre sortie est bien anunlée");
            return $this ->redirectToRoute('sortie_list');
        }

        #[Route('/sortie/details/{id}', name: 'sortie_details')]
            public function details (int $id, SortieRepository $sortieRepository): Response{
            $sortie= $sortieRepository->find($id);

            return $this->render('sortie/detail.html.twig', [
                        "sortie"=>$sortie
            ]);
        }

        #[Route('/sortie/list', name: 'sortie_list')]
        public function list (SortieRepository $sortieRepository,Request $request): Response{

                $rechercheDonnee = [
                    'je_suis_organisateur' => false,
                    'sortie_inscrit' => false,
                    'sortie_non_inscrit' => false,
                    'date_min' => new \DateTime("-1 month"),
                    'date_max'=> new \DateTime("+1 year"),

            ];
                $rechercheForm =$this ->createForm(SortieRechercheType::class,$rechercheDonnee);
                $rechercheForm->handleRequest($request);

                $rechercheDonnee = $rechercheForm->getData();

                $rechercheSorties = $sortieRepository->chercher($this->getUser(),$rechercheDonnee);

            return $this->render('sortie/list.html.twig', [
               'rechercheForm'=>$rechercheForm->createView(),
                'rechercheSorties'=>$rechercheSorties
            ]);
        }



    /**
     * @Route("/{id}/publier", name="publish")
     */
    #[Route('/sortie/publish', name: 'sortie_publish')]
    public function publish(Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        //vérifie que c'est bien l'auteur (ou un admin) qui est en train de publier
        if ($this->getUser() !== $sortie->getOrganisateur() && !$this->isGranted("ROLE_ADMIN")) {
            throw $this->createAccessDeniedException("Seul l'auteur de cette sortie peut la publier !");
        }
        $newEtat =$etatRepository->findOneBy(['libelle'=>'Ouverte']);
        $sortie->setEtat($newEtat);
        $entityManager->persist($sortie);
        $entityManager->flush();


        $this->addFlash('success', 'La sortie est publiée !');

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }





}


