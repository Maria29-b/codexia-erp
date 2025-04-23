<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeeDashboardController extends AbstractController
{
    #[Route('/employee/dashboard', name: 'employee_dashboard')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function index(PrestationRepository $prestationRepository): Response
    {
        $user = $this->getUser();

        // On récupère les prestations liées à cet employé
        $prestations = $prestationRepository->findBy(['employee' => $user]);

        return $this->render('employee_dashboard/index.html.twig', [
            'prestations' => $prestations,
        ]);
    }
}
