<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Prestation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Get existing prestations and users
        $prestations = $manager->getRepository(Prestation::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($prestations as $prestation) {
            // 30% chance to add comments
            if ($faker->boolean(30)) {
                $commentCount = $faker->numberBetween(1, 3);

                for ($i = 0; $i < $commentCount; $i++) {
                    $comment = new Commentaire();
                    $comment->setContenu($faker->paragraph);
                    $comment->setDateCreation(
                        $faker->dateTimeBetween(
                            $prestation->getDate()->format('Y-m-d H:i:s'),
                            'now'
                        )
                    );
                    $comment->setPrestation($prestation);
                    $comment->setAuteur($faker->randomElement($users));

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PrestationFixtures::class,
            UserFixture::class,
        ];
    }
}