<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255]),
                ]
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('zoneIntervention', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}