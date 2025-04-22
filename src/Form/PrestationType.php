<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Prestation;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('heureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('statut')
            ->add('prixTotal')
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}
