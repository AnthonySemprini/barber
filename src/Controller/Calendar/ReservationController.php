<?php

namespace App\Controller\Calendar;

use App\Form\TextType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
        $availableSlots = [];
        $isoDate = null;
        if ($selectedDate) {
            // Convertissez la date  au format 'Y-m-d' 
            $dateTime = \DateTime::createFromFormat('d/m/Y', $selectedDate);

            if (false !== $dateTime) {
                // La date a été correctement analysée
                $isoDate = $dateTime->format('Y-m-d');

                // Récupérez les réservations pour la date sélectionnée
            } else {
            
            }
            if (null !== $isoDate) {
            // Récupérez les réservations pour la date sélectionnée
            $dql = "SELECT r
                FROM App\Entity\Reservation r
                WHERE SUBSTRING(r.rdv, 1, 10) = :isoDate";
            $query = $entityManager->createQuery($dql);
            $query->setParameter('isoDate', $isoDate);
            $rdvs = $query->getResult();
        } else {
            $rdvs = []; 
        }
   
        // Créez un tableau pour stocker les créneaux d'une journée
        $startTime = strtotime('08:00');
        $endTime = strtotime('18:00');
        $interval = 30 * 60; // 30 minutes en secondes
    

// Créez un tableau avec tous les créneaux horaires de la journée
$slots = [];
while ($startTime < $endTime) {
    $slotStart = date('H:i', $startTime);
    $slots[] = $slotStart; // on stocke seulement l'heure de début, l'heure de fin est calculée
    $startTime += $interval;
}


$availableSlots = $slots;

foreach ($rdvs as $event) {
    // Convertissez le datetime de l'événement en une chaîne de créneau horaire pour la comparaison
    $eventStartString = $event->getRdv()->format('H:i');
    $eventEnd = clone $event->getRdv();
    $eventEnd->modify('+30 minutes'); //   RDV dure 30 minutes
    $eventEndString = $eventEnd->format('H:i');

    // Filtrez $availableSlots pour enlever les créneaux occupés par cet événement
    $availableSlots = array_filter($availableSlots, function ($slot) use ($eventStartString, $eventEndString) {
        $slotEnd = \DateTime::createFromFormat('H:i', $slot)->modify('+30 minutes')->format('H:i');
        return $slot != $eventStartString && $slotEnd != $eventEndString;
    });
}

// Transformez $availableSlots en un tableau de plages horaires avec début et fin
$finalSlots = [];
foreach ($availableSlots as $slot) {
    $slotEnd = \DateTime::createFromFormat('H:i', $slot)->modify('+30 minutes')->format('H:i');
    $finalSlots[] = ['start' => $slot, 'end' => $slotEnd];
}
// dd($finalSlots);
        $reservation = new Reservation();

  // Récupérez l'utilisateur connecté
        $user = $this->security->getUser();
        
        if (!$user) {
            // Gérez le cas où aucun utilisateur n'est connecté si nécessaire
            throw $this->createAccessDeniedException('Vous devez être connecté pour effectuer cette action.');
            
            return $this->redirectToRoute('app_login');
        }
        
        // Associez l'utilisateur à la réservation
        $reservation->setUser($user);
        
        // Créez et traitez le formulaire
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegardez la réservation avec l'utilisateur associé
            
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Redirigez ou affichez un message de succès comme nécessaire
            return $this->redirectToRoute('app_reservation_success');
        }

return $this->render('reservation/new.html.twig', [
    'selectedDate' => $selectedDate,
    'rdvs' => $rdvs,
    'availableSlots' => $finalSlots,
    'form' => $form->createView(),
    ]);
} 
    
}
    
#[Route('/succes', name: 'app_reservation_success')]
public function success(): Response
{
    return $this->render('reservation/success.html.twig');
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


