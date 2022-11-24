<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'html5'=>true,
                'widget'=>'single_text'
                ])
            ->add('nbParticipantMax', IntegerType::class,[
                'label'=> 'Nombre de places'
            ])
            ->add('duree', IntegerType::class,[
                'label'=> 'Durée (en minutes)'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'incription ",
                 'html5'=>true,
                'widget'=>'single_text'
            ])

            ->add('infoSortie',TextareaType::class,[
                'label'=>'Information Sortie',
            ])
            ->add('campusOrganisateur', EntityType::class,[
                'label'=>'Campus',
              'class'=> Campus::class,
                'choice_label'=> 'nom'
                ])
            ->add('lieu',EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=> 'nom'
            ])
            ->add('etat',EntityType::class,[
                'class'=>Etat::class,
                'choice_label'=>'libelle'
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
