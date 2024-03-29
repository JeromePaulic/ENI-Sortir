<?php
namespace  App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieRechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->setMethod('get')
            ->add('campus_nom',EntityType::class,[
                'label'=> 'Campus',
                'class'=> Campus::class,
                'choice_label'=> 'nom',
                'required'=>false,
                ])
            ->add('rechercher', SearchType::class,[
                'label'=>'Le nom de la sortie contient',
                'required'=>false,
            ])
            ->add('date_min',DateType::class,[
                'label'=>'Entre le',
                'html5'=>false,
                'widget'=>'single_text',
                'attr'=>['class'=>'datepicker'],
                'format'=>'dd/MM/yyyy'
            ])
            ->add('date_max',DateType::class,[
                'label'=>'Et le',
                'html5'=>false,
                'widget'=>'single_text',
                'attr'=>['class'=>'datepicker'],
                'format'=>'dd/MM/yyyy'
            ])
            ->add('je_suis_organisateur',CheckboxType::class,[
                'label'=>"Sorties dont je suis l'organisateur/trice",
                'required'=>false,
            ])
            ->add('sortie_inscrit',CheckboxType::class,[
                'label'=> 'Sorties auxquelles je suis inscrit/e',
                'required'=>false,
            ])
            ->add('sortie_non_inscrit',CheckboxType::class,[
                'label'=>'Sorties auxquelles je ne suis pas inscrit/e',
                'required'=>false,
            ])
            ->add('sortie_passee',CheckboxType::class,[
                'label'=>'Sorties passées',
                'required'=>false,
            ])
            ->add('submit',SubmitType::class,[
                'label'=>'Rechercher'
            ])
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //pas besoin de protection csrf ici
            'csrf_protection' => false

        ]);
    }


}