<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\PrestationRepository;
use App\Repository\ClientRepository;
use App\Repository\EmployeeRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/prestation')]
final class PrestationController extends AbstractController
{
    #[Route('/', name: 'app_prestation_index')]
    public function index(
        Request $request,
        PrestationRepository $prestationRepository,
        ClientRepository $clientRepo,
        EmployeeRepository $employeeRepo,
        ServiceRepository $serviceRepo,
        PaginatorInterface $paginator
    ): Response {
        $filters = $request->query->all();

        $queryBuilder = $prestationRepository->getFilteredQueryBuilder($filters);

        $prestations = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
            'clients' => $clientRepo->findAll(),
            'employees' => $employeeRepo->findAll(),
            'services' => $serviceRepo->findAll(),
            'selectedFilters' => $filters,
        ]);
    }

    #[Route('/new', name: 'app_prestation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepo): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $duree = $prestation->getDuree(); // in minutes
            $prixHoraire = $prestation->getService()->getPrixHoraire(); // per hour
            $prixTotal = ($duree / 60) * $prixHoraire;
            $prestation->setPrixTotal($prixTotal);
            $entityManager->persist($prestation);
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestation/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form,
            'services' => $serviceRepo->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_prestation_show', methods: ['GET'])]
    public function show(Prestation $prestation): Response
    {
        return $this->render('prestation/show.html.twig', [
            'prestation' => $prestation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prestation $prestation, EntityManagerInterface $entityManager, ServiceRepository $serviceRepo): Response
    {
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestation/edit.html.twig', [
            'prestation' => $prestation,
            'form' => $form,
            'services' => $serviceRepo->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_prestation_delete', methods: ['POST'])]
    public function delete(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prestation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($prestation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
    }

    // Ajoutez cette méthode

    #[Route('/employee/prestation/{id}/complete', name: 'employee_prestation_complete')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function markAsComplete(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'employé a le droit de modifier cette prestation
        $currentEmployee = $this->getUser()->getEmploye();
        if ($prestation->getEmployee()->getId() !== $currentEmployee->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cette prestation.');
        }

        // Modifier le statut
        $prestation->setStatut('réalisé');
        $entityManager->flush();

        $this->addFlash('success', 'La prestation a été marquée comme réalisée.');

        return $this->redirectToRoute('employee_calendar');
    }

    #[Route('/my-prestations', name: 'employee_my_prestations')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function myPrestations(
        PrestationRepository $prestationRepository
    ): Response {
        $currentEmployee = $this->getUser()->getEmployee();

        if (!$currentEmployee) {
            throw $this->createNotFoundException('Aucun employé associé à cet utilisateur.');
        }

        $prestations = $prestationRepository->findByEmployee($currentEmployee);

        return $this->render('prestation/my_prestations.html.twig', [
            'prestations' => $prestations,
        ]);
    }

}
