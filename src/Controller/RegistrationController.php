<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà connecté.');
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Encoder le mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Par défaut, attribuer le rôle employé
                $user->setRoles(['ROLE_EMPLOYEE']);

                // Persister l'utilisateur
                $entityManager->persist($user);

                // Créer l'entité Employe associée
                $employee = new Employee();
                $employee->setNom($user->getNom());
                $employee->setPrenom($user->getPrenom());
                $employee->setEmail($user->getEmail());
                $employee->setZoneIntervention($form->get('zoneIntervention')->getData());
                $employee->setUser($user);

                $entityManager->persist($employee);
                $entityManager->flush();

                // Ajouter un message de succès
                $this->addFlash('success', 'Votre compte a été créé avec succès.');

                // Authentifier l'utilisateur automatiquement
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}