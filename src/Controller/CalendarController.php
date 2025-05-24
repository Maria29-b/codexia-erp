<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    #[Route('/calendar/events', name: 'calendar_events')]
    public function events(PrestationRepository $prestationRepository, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $prestations = [];

        if ($isAdmin) {
            $prestations = $prestationRepository->findAll();
        } elseif ($this->isGranted('ROLE_EMPLOYEE')) {
            $employee = $user->getEmploye();
            $prestations = $prestationRepository->findBy(['employee' => $employee]);
        }

        $events = [];

        foreach ($prestations as $prestation) {
            $start = (clone $prestation->getDate())
                ->setTime((int)$prestation->getHeureDebut()->format('H'), (int)$prestation->getHeureDebut()->format('i'));

            $end = (clone $start)->modify("+{$prestation->getDuree()} minutes");

            $events[] = [
                'id' => $prestation->getId(),
                'title' => $prestation->getService()->getNom() . ' - ' . $prestation->getClient()->getNom(),
                'start' => $start->format('Y-m-d\TH:i:s'),
                'end' => $end->format('Y-m-d\TH:i:s'),
                'url' => $isAdmin ?
                    $this->generateUrl('app_prestation_show', ['id' => $prestation->getId()]) :
                    $this->generateUrl('employee_prestation_edit', ['id' => $prestation->getId()]),
                'color' => $prestation->getStatut() === 'réalisé' ? '#28a745' : '#007bff',
            ];
        }

        return new JsonResponse($events);
    }
    #[Route('/admin/calendar', name: 'admin_calendar')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('calendar/index.html.twig');
    }

    #[Route('/employee/calendar', name: 'employee_calendar')]
    public function employeeCalendar(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');
        return $this->render('calendar/employee.html.twig');
    }
}
