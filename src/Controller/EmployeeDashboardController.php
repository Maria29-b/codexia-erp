<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use App\Entity\Prestation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeeDashboardController extends AbstractController
{
    #[Route('/employee/dashboard', name: 'employee_dashboard')]
    #[IsGranted('ROLE_EMPLOYEE')]    public function index(PrestationRepository $prestationRepository): Response
    {
        $user = $this->getUser();
        $employee = $user->getEmploye();
        
        // Filter only future prestations and sort chronologically
        $today = new \DateTime();
        $today->setTime(0, 0); // Set time to beginning of day
        
        $prestations = $prestationRepository->createQueryBuilder('p')
            ->where('p.employee = :employee')
            ->andWhere('p.date >= :today')
            ->andWhere('p.statut != :status_realise')
            ->setParameter('employee', $employee)
            ->setParameter('today', $today)
            ->setParameter('status_realise', 'réalisé')
            ->orderBy('p.date', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('employee_dashboard/index.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/employee/dashboard/past', name: 'employee_dashboard_past')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function pastPrestations(PrestationRepository $prestationRepository): Response
    {
        $user = $this->getUser();
        $employee = $user->getEmploye();

        // Filter only past prestations and sort chronologically
        $prestations = $prestationRepository->createQueryBuilder('p')
            ->where('p.employee = :employee')
            ->andWhere('p.statut = :status_realise')
            ->setParameter('employee', $employee)
            ->setParameter('status_realise', 'réalisé')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('employee_dashboard/past.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/employee/prestation/{id}/done', name: 'employee_mark_done', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function markAsDone(Prestation $prestation, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Security: make sure the employee owns the prestation
        if ($prestation->getEmployee() !== $user->getEmploye()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette prestation.");
        }

        $prestation->setStatut('réalisé');
        $em->flush();

        $this->addFlash('success', 'Prestation marquée comme réalisée.');
        return $this->redirectToRoute('employee_dashboard_past');
    }


    #[Route('/employee/prestation/{id}/edit', name: 'employee_prestation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function edit(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
    {
        // Verify that the prestation belongs to the current employee
        if ($prestation->getEmployee() !== $this->getUser()->getEmploye()) {
            throw $this->createAccessDeniedException('You can only edit your own prestations.');
        }

        if ($request->isMethod('POST')) {
            $statut = $request->request->get('statut');
            if ($statut) {
                $prestation->setStatut($statut);
                $entityManager->flush();
                $this->addFlash('success', 'Statut de la prestation mis à jour avec succès');
                return $this->redirectToRoute('employee_dashboard');
            }
        }

        return $this->render('employee_dashboard/edit.html.twig', [
            'prestation' => $prestation,
        ]);
    }

}
