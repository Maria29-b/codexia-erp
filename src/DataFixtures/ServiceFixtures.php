<?php


namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = [
            [
                'nom' => 'Nettoyage standard',
                'prixHoraire' => '25.00',
                'description' => 'Nettoyage de base des pièces principales'
            ],
            [
                'nom' => 'Nettoyage approfondi',
                'prixHoraire' => '35.00',
                'description' => 'Nettoyage complet avec attention aux détails'
            ],
            [
                'nom' => 'Nettoyage vitres',
                'prixHoraire' => '30.00',
                'description' => 'Nettoyage intérieur et extérieur des vitres'
            ],
            [
                'nom' => 'Nettoyage après déménagement',
                'prixHoraire' => '40.00',
                'description' => 'Nettoyage complet après départ des occupants'
            ],
            [
                'nom' => 'Nettoyage de printemps',
                'prixHoraire' => '45.00',
                'description' => 'Grand nettoyage annuel complet'
            ],
        ];

        foreach ($services as $data) {
            $service = new Service();
            $service->setNom($data['nom']);
            $service->setPrixHoraire($data['prixHoraire']);
            $service->setDescription($data['description']);

            $manager->persist($service);
        }

        $manager->flush();
    }
}