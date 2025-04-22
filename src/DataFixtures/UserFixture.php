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
        // Création de l'admin
        $admin = new User();
        $admin->setEmail('admin@nettoyage.com');
        $admin->setNom('Durand');
        $admin->setPrenom('Marie');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'admin123'
        ));
        $manager->persist($admin);

        // Création d'employés
        $employeData = [
            [
                'email' => 'pierre@nettoyage.com',
                'nom' => 'Martin',
                'prenom' => 'Pierre',
                'zone' => 'Nord',
            ],
            [
                'email' => 'sophie@nettoyage.com',
                'nom' => 'Petit',
                'prenom' => 'Sophie',
                'zone' => 'Sud',
            ],
            [
                'email' => 'thomas@nettoyage.com',
                'nom' => 'Dubois',
                'prenom' => 'Thomas',
                'zone' => 'Est',
            ],
        ];

        foreach ($employeData as $data) {
            // Création du User
            $user = new User();
            $user->setEmail($data['email']);
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setRoles(['ROLE_EMPLOYEE']);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'employe123'
            ));
            $manager->persist($user);

            // Création de l'Employe associé
            $employee = new Employee();
            $employee->setNom($data['nom']);
            $employee->setPrenom($data['prenom']);
            $employee->setEmail($data['email']);
            $employee->setZoneIntervention($data['zone']);
            $employee->setUser($user);
            $manager->persist($employee);
        }

        $manager->flush();
    }
}
