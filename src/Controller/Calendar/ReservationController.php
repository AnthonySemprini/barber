<?php

namespace App\Controller\Calendar;

use App\Form\TextType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    
    #[Route('/', name: 'app_reservation', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        //redirige vers la page reservation
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    
    
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, PrestationRepository $prestationRepository, SessionInterface $session, ReservationRepository $reservationRepository, Request $request): Response
    {
    
        $selectedDate = $request->request->get('daySelect');
        // dd($selectedDate);
    if ($selectedDate) {
        // Convertissez la date sélectionnée au format 'Y-m-d' pour correspondre à la base de données
        $isoDate = \DateTime::createFromFormat('d/m/Y', $selectedDate)->format('Y-m-d');

        // Récupérez les réservations pour la date sélectionnée
        $dql = "SELECT r
            FROM App\Entity\Reservation r
            WHERE SUBSTRING(r.rdv, 1, 10) = :isoDate";
        $query = $entityManager->createQuery($dql);
        $query->setParameter('isoDate', $isoDate);
        $rdvs = $query->getResult();
    } else {
        $rdvs = []; // Aucune date sélectionnée, donc aucun rdv
    }
    //create array crenaux d'une journée
    
    $startTime = strtotime('08:00');
    $endTime = strtotime('18:00');
    $interval = 30 * 60; // 30 minutes en secondes
    
    $slots = []; // Tableau pour stocker les créneaux d'une jouenée
    $removedSlots = []; // Tableau pour stocker les créneaux supprimés
    
    // Crée un tableau avec tous les créneaux horaires de la journée
    while ($startTime < $endTime) {
        $slotStart = date('H:i', $startTime);
        $startTime += $interval;
        $slotEnd = date('H:i', $startTime);
        $slots[] = ['start' => $slotStart, 'end' => $slotEnd];
    }
    
    //         // Supprime les créneaux où des événements sont planifiés
    // $events = [
    //         ['start' => '09:00', 'end' => '11:30'],
    //         ['start' => '11:30', 'end' => '12:30'],
    //         ['start' => '11:00', 'end' => '11:30'],
    //         ['start' => '15:30', 'end' => '16:00'],
    //         ['start' => '17:30', 'end' => '18:00'],
    //         // Ajoutez d'autres événements avec leurs heures de début et de fin
    //     ];

        
        foreach ($rdvs as $event) {
            // dd($rdvs);
            $eventStart = $event->getRdv('H:i');
            //  dd($eventStart);
            $eventEnd = $event->getRdv();
            $eventEnd->modify('+30 minutes');
            // dd($eventEnd);
            
            
            $slots = array_filter($slots, function ($slot) use ($eventStart, $eventEnd, &$removedSlots) {
                $slotStart = strtotime($slot['start']);
                $slotEnd = strtotime($slot['end']);
                $isOverlapping = ($slotStart >= $eventEnd->getTimestamp() || $slotEnd <= $eventStart->getTimestamp());
                dd($isOverlapping);
                
                if ($isOverlapping == false) {
                    $removedSlots[] = $slot;
                    
               
                }
                
                return $isOverlapping;
            });
            // dd($slots);
            
        }

        return $this->render('reservation/new.html.twig', [
            'selectedDate' => $selectedDate,
            'rdvs' => $rdvs,
            'slots' => $slots
        ]);
    }

    
    
 
     


    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {

        // Créer un formulaire de réservation pour la modification
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //enrengistre la modification en BDD
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si le jeton CSRF (Cross-Site Request Forgery) est valide
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            //supprime la reservattion en BDD
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
    }




    // #[Route('/creneaux', name: 'app_reservation_crenaux', methods: ['POST'])]
    // public function creneauxHorairesAction(Request $request): JsonResponse
    // {
    //     // Récupérer la date sélectionnée depuis la requête POST
    //     $selectedDate = $request->request->get('selectedDate');

    //     // Vous avez maintenant accès à la variable $selectedDate dans votre contrôleur
    //     // Vous pouvez l'utiliser pour effectuer le traitement nécessaire, par exemple, pour obtenir les créneaux horaires.

    //     // Exemple : obtenez les créneaux horaires pour la date sélectionnée depuis votre source de données
    //     $creneauxHoraires = $this->getCreneauxHoraires($selectedDate);

    //     // Retournez les créneaux horaires au format JSON
    //     return new JsonResponse($creneauxHoraires);
    // }

} 


