<?php

// src/Form/CongesType.php

namespace App\Form;

use App\Entity\Conges;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CongesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif',
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif du congé'
            ])->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur'
            ]);

        // Ajoutez le champ utilisateur seulement pour les admins
        if ($options['is_admin']) {
            $builder->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'email', // ou une méthode comme getFullName()
                'attr' => ['class' => 'form-select'],
                'label' => 'Employé'
            ])
                ->add('estValide', ChoiceType::class, [
                    'choices' => [
                        'En attente' => null,
                        'Approuvé' => true,
                        'Refusé' => false
                    ],
                    'attr' => ['class' => 'form-select'],
                    'label' => 'Statut'
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conges::class,
            'is_admin' => false // Option par défaut
        ]);
    }
}
