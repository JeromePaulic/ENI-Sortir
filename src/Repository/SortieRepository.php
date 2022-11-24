<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository

{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);

    }


    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function trouverJointure(int $id)
{
    $queryBuilder =$this ->createQueryBuilder('s');
    $queryBuilder
        ->andWhere('s.id = :id')->setParameter(':id',$id)

        ->leftJoin('s.etat','e')->addSelect('e')
        ->leftJoin('s.organisateur','o')->addSelect('o')
        ->leftJoin('s.inscriptions','i')->addSelect('i')
        ->leftJoin('inscription.participants','participate')->addSelect('participate')
        ->leftJoin('s.lieu','loc')->addSelect('loc');

            return $queryBuilder->getQuery()->getOneOrNullResult();


}


    /**
     *
     * @param UserInterface $user
     * @param array|null $rechercheDonnee
     * @return array| mixed
     */
    public function chercher( UserInterface $user, ?array $rechercheDonnee)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->select('s');




       $etatRepository = $this->getEntityManager()->getRepository(Etat::class);

        //que les sorties ouvertes par défaut + sorties créées par moi
        $sortieOuverte = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $sortieCree = $etatRepository->findOneBy(['libelle' => 'Créée']);
        $sortieFermee = $etatRepository->findOneBy(['libelle' => 'Cloturée']);



        //ajoute des clauses where par défaut, toujours présentes
        $queryBuilder->andWhere('
                            s.etat = :sortieOuverte OR s.etat = :sortieFermee
                            OR (s.etat = :sortieCree AND s.organisateur = :user)
                                ')
            ->setParameter('sortieOuverte', $sortieOuverte)
            ->setParameter('sortieFermee', $sortieFermee)
            ->setParameter('user', $user)
            ->setParameter('sortieCree', $sortieCree);



        //jointures toujours présentes, pour éviter que doctrine fasse 10000 requêtes
        $queryBuilder->leftJoin('s.inscriptions', 'i')
            ->addSelect('i')
            ->leftJoin('s.organisateur', 'o')
            ->addSelect('o');


        //la plus proche dans le temps en 1er

        $queryBuilder->orderBy('s.dateHeureDebut', 'ASC');

        //recherche par mot-clef, si applicable
        if (!empty($rechercheDonnee ['rechercher'])) {
            $queryBuilder->andWhere('s.nom LIKE :rech')
                ->setParameter('rech', '%' . $rechercheDonnee['rechercher'] . '%');
        }

        //filtre par site
        if (!empty($rechercheDonnee['campus_nom'])) {
            $queryBuilder->andWhere('s.campusOrganisateur=:campus')
                ->setParameter('campus', $rechercheDonnee['campus_nom']);
        }


        //filtre date mini
        if (!empty($rechercheDonnee['date_min'])) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :date_min')
                ->setParameter('date_min', $rechercheDonnee['date_min']);
        }


       //filtre date max
        if (!empty($rechercheDonnee['date_max'])) {
            $queryBuilder->andWhere('s.dateHeureDebut <= :date_max')
                ->setParameter('date_max', $rechercheDonnee['date_max']);
        }


        //création d'un ensemble de condition OR entre les parenthèses

        $checkBoxesOr =$queryBuilder->expr()->orX();


        //récupère l'ids des sorties auxquelles je suis inscrit dans une autre requête
        //ça nous donne un array contenant les ids, qui sera utile pour les IN ou NOT IN plus loin

        $inscritQueryBuilder =$this ->createQueryBuilder('s');
        $inscritQueryBuilder
            ->from(Inscription::class,'i')->select("DISTINCT (ev.id)")
            ->join('i.sortie','ev')->setParameter("user", $user)
            ->andWhere('i.participants = :user');

        $result = $inscritQueryBuilder->getQuery()->getScalarResult();


       $subcribedToEventIds= array_column($result,"1");

        //inclure les sorties auxquelles je suis inscrit
        if(!empty($rechercheDonnee[ 'sortie_inscrit'])){
            $checkBoxesOr->add($queryBuilder->expr()->in('i.sortie',$subcribedToEventIds));
        }

        //inclure les sorties auxquelles je ne suis pas inscrit
        if(!empty($rechercheDonnee['sortie_non_inscrit'])){
            $checkBoxesOr->add($queryBuilder->expr()->notIn('i.sortie',$subcribedToEventIds));
        }
        //inclure les sorties dont je suis l'organisateur
        if (!empty($rechercheDonnee['je_suis_organisateur'])){
            $checkBoxesOr->add($queryBuilder->expr()->eq('o', $user->getId()));
        }
        //maintenant que nos clauses OR regroupées sont créées, on les ajoute à la requête dans un grand AND()

        $queryBuilder->andWhere($checkBoxesOr);

        $count = count($queryBuilder->getQuery()->getResult());

        //on récupère les résultats, en fonction des filtres précédent
        $query = $queryBuilder->getQuery();

        return $query->getResult();



    }
}
