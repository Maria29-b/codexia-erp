<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\PrestationRepository;
use App\Repository\ClientRepository;
use App\Repository\EmployeeRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        ServiceRepository $serviceRepo
    ): Response {
        $filters = $request->query->all();

        $prestations = $prestationRepository->findByFilters($filters);

        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
            'clients' => $clientRepo->findAll(),
            'employees' => $employeeRepo->findAll(),
            'services' => $serviceRepo->findAll(),
            'selectedFilters' => $filters,
        ]);
    }

    #[Route('/new', name: 'app_prestation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prestation);
            $entityManager->flush();

            return $this->redirectToRoute('app_prestation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prestation/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prestation_show', methods: ['GET'])]
    public function show(Prestation $prestation): Response
    {
        return $this->render('prestation/show.html.twig', [
            'prestation' => $prestation,
        ]);
    }

    #[Route('/{id}/pdf', name: 'admin_prestation_pdf', methods: ['GET'])]
    public function generatePdf(Prestation $prestation): Response
    {
        // 1. Générer le HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Facture Prestation</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 30px;
                    border: 1px solid #ccc;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                }
                .header h1 {
                    margin-bottom: 5px;
                }
                .contact-info {
                    font-size: 0.9em;
                    color: #666;
                }
                .details {
                    margin-bottom: 20px;
                }
                .details h2 {
                    font-size: 1.2em;
                    margin-bottom: 10px;
                }
                .total {
                    font-size: 1.2em;
                    font-weight: bold;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Votre Entreprise</h1>
                    <p class="contact-info">123 Rue de Paris<br>contact@entreprise.com</p>
                </div>

                <div class="details">
                    <h2>Facture Prestation #' . $prestation->getId() . '</h2>
                    <p><strong>Client :</strong> ' . $prestation->getClient()->getPrenom() . ' ' . $prestation->getClient()->getNom() . '</p>
                    <p><strong>Date :</strong> ' . $prestation->getDate()->format('d/m/Y') . '</p>
                </div>

                <div class="total">
                    Total à payer : ' . number_format($prestation->getPrixTotal(), 2, ',', ' ') . ' €
                </div>
            </div>
        </body>
        </html>';

        // 2. Initialiser Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        
        // 3. Rendre le PDF
        $dompdf->render();
    
        // 4. Ajouter le footer (APRÈS le render())
        $canvas = $dompdf->getCanvas();
        $footer = "Page {PAGE_NUM} sur {PAGE_COUNT} | Généré le " . date('d/m/Y');
        $canvas->page_text(270, 820, $footer, null, 10, [0, 0, 0]);
    
        // 5. Générer le nom du fichier
        $filename = sprintf('prestation-%d-%s.pdf', $prestation->getId(), date('Ymd-His'));
    
        // 6. Retourner la réponse
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]
        );
    }

    #[Route('/{id}/edit', name: 'app_prestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prestation $prestation, EntityManagerInterface $entityManager): Response
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
}
