<?php

namespace App\Form;

use App\Entity\Tache;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre')
            ->add('description')
            ->add('dateDebut')
            ->add('estFait')
            ->add('partagerAvec', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Partager avec d’autres utilisateurs'
            ])
            ->add('Fichier', FileType::class, [
                'label' => 'Fichiers à joindre',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ])
            ->add('deadline', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date limite'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
