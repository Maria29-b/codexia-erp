<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $zones = ['Nord', 'Sud', 'Est', 'Ouest', 'Centre'];

        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setNom($faker->lastName);
            $client->setPrenom($faker->firstName);
            $client->setEmail($faker->email);
            $client->setTelephone($faker->regexify('0[1-9]\d{8}'));
            $client->setZoneIntervention($faker->randomElement($zones));

            $manager->persist($client);
        }

        $manager->flush();
    }
}