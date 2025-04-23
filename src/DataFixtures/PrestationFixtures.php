<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Service;
use App\Entity\Prestation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class PrestationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $clients = $manager->getRepository(Client::class)->findAll();
        $employees = $manager->getRepository(Employee::class)->findAll();
        $services = $manager->getRepository(Service::class)->findAll();

        $statuts = ['a_planifier', 'confirme', 'realise'];

        for ($i = 0; $i < 50; $i++) {
            $prestation = new Prestation();

            // Set date (past dates only)
            $prestation->setDate($faker->dateTimeBetween('-1 year', 'now'));

            // Set start time (8AM to 4PM)
            $startTime = $faker->numberBetween(8, 16);
            $heureDebut = \DateTime::createFromFormat('H:i:s', sprintf('%02d:00:00', $startTime));
            $prestation->setHeureDebut($heureDebut);

            // Set duration (30-240 mins)
            $prestation->setDuree($faker->numberBetween(30, 240));

            // Other fields
            $prestation->setStatut($faker->randomElement($statuts));

            // Calculate price
            $service = $faker->randomElement($services);
            $hours = $prestation->getDuree() / 60;
            $price = $hours * (float)$service->getPrixHoraire();
            $prestation->setPrixTotal(number_format($price, 2, '.', ''));

            // Set relations
            $prestation->setClient($faker->randomElement($clients));
            $prestation->setEmployee($faker->randomElement($employees));
            $prestation->setService($service);

            $manager->persist($prestation);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
            UserFixture::class,
            ServiceFixtures::class,
        ];
    }
}