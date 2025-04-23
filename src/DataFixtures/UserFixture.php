<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin user
        $admin = new User();
        $admin->setEmail('admin@nettoyage.com');
        $admin->setNom('Durand');
        $admin->setPrenom('Marie');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Employee users
        $employeData = [
            ['pierre@nettoyage.com', 'Martin', 'Pierre', 'Nord'],
            ['sophie@nettoyage.com', 'Petit', 'Sophie', 'Sud'],
            ['thomas@nettoyage.com', 'Dubois', 'Thomas', 'Est'],
            ['client1@example.com', 'Client', 'Jean', 'Ouest'], // Sample client user
            ['client2@example.com', 'Client', 'Sophie', 'Centre'], // Sample client user
        ];

        foreach ($employeData as $data) {
            $user = new User();
            $user->setEmail($data[0]);
            $user->setNom($data[1]);
            $user->setPrenom($data[2]);

            // First 3 are employees, last 2 are clients
            $roles = (str_contains($data[0], 'client')) ? ['ROLE_CLIENT'] : ['ROLE_EMPLOYEE'];
            $user->setRoles($roles);

            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $manager->persist($user);

            // Only create Employee records for actual employees
            if (!str_contains($data[0], 'client')) {
                $employee = new Employee();
                $employee->setNom($data[1]);
                $employee->setPrenom($data[2]);
                $employee->setEmail($data[0]);
                $employee->setZoneIntervention($data[3]);
                $employee->setUser($user);
                $manager->persist($employee);
            }
        }

        $manager->flush();
    }
}