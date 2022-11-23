<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */

    public function profil()
    {
        return $this->render('profil/profil.html.twig');
    }





    /**
     * @Route("/editprofil", name="editprofil")
     * @param Request $Request
     * @param $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function editprofil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $profilForm = $this->createForm(ProfilType::class, $user);


        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        //todo traiter le formulaire

        return $this->render('profil/editprofil.html.twig', [
            'profilForm' => $profilForm->createView()
        ]);
    }






    /**
     * @Route("/editpass", name="editpass")
     * @param Request $Request
     * @param $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function editpass(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher )
    {
        if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            // On vérifie si les 2 mdp sont identiques
            if($request->request->get('pass') == $request->request->get('pass2')){
                $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('pass')));
                $em->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec succès');

                return $this->redirectToRoute('profil');
            }else{
                $this->addFlush('error', 'Les 2 mots de passe ne sont pas identiques');
            }
        }


        return $this->render('profil/editpass.html.twig');
    }






    /**
     * Affichage du profil d'un user
     *
     * @Route("/profiluser/{id}", name="profiluser", requirements={"id": "\d+"})
     */
    public function profiluser(User $user): Response
    {

        return $this->render('profil/profiluser.html.twig', [
            'user' => $user
        ]);
    }





}