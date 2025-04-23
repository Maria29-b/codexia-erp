<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Prestation;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la prestation',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une date']),
                ],
            ])
            ->add('heureDebut', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une heure de début']),
                ],
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer une durée']),
                    new GreaterThanOrEqual([
                        'value' => 1,
                        'message' => 'La durée doit être d\'au moins 1 minute',
                    ]),
                ],
            ])
            ->add('statut', TextType::class, [
                'label' => 'Statut',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer un statut']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le statut ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('prixTotal', NumberType::class, [
                'label' => 'Prix total (€)',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le prix total']),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le prix ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'label' => 'Client',
                'choice_label' => function (Client $client) {
                    return $client->getNom() . ' ' . $client->getPrenom();
                },
                'placeholder' => 'Sélectionnez un client',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un client']),
                ],
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'label' => 'Employé',
                'choice_label' => function (Employee $employee) {
                    return $employee->getNom() . ' ' . $employee->getPrenom();
                },
                'placeholder' => 'Sélectionnez un employé',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un employé']),
                ],
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'label' => 'Service',
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un service',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un service']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}
