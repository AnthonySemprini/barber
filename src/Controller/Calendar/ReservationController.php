<?php

namespace App\Controller\Calendar;

use App\Form\TextType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\Security\Core\Security;
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


    #[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function newResrvation($id, EntityManagerInterface $entityManager, PrestationRepository $prestationRepository, SessionInterface $session, ReservationRepository $reservationRepository, Request $request,PaginatorInterface $paginator, MailerInterface $mailer): Response
    {
        //! Partie qui recup la date selectionner et qui renvoi les crenaux dispo de la date en question

        $selectedDate = $request->request->get('daySelect'); 

        $availableSlots = [];
        $isoDate = null;

        if ($selectedDate) {

            $isoDate = $selectedDate;

            $rdvs = $reservationRepository->findReservationBooked($isoDate);

        } else {
            $rdvs = [];
        }


        // Créez un tableau pour stocker les créneaux d'une journée

        $startTime = strtotime('08:00');
        $endTime = strtotime('18:00');
        $interval = 30 * 60;
        $slots = [];
        while ($startTime < $endTime) {
            $slotStart = date('H:i', $startTime);
            $slots[] = $slotStart;
            $startTime += $interval;
        }
        $availableSlots = $slots;

        foreach ($rdvs as $event) {
            // Convertir le datetime de l'événement en une chaîne de créneau horaire pour la comparaison
            $eventStartString = $event->getRdv()->format('H:i');
            $eventEnd = clone $event->getRdv();
            // Obtenez la durée
            $eventEnd->modify('+30 minutes'); //   RDV dure 30 minutes
            $eventEndString = $eventEnd->format('H:i');
            // dd($eventEndString);

            // Filtre $availableSlots pour enlever les créneaux occupés par cet événement
            $availableSlots = array_filter($availableSlots, function ($slot) use ($eventStartString, $eventEndString) {
                $slotEnd = \DateTime::createFromFormat('H:i', $slot)->modify('+30 minutes')->format('H:i');
                return $slot != $eventStartString && $slotEnd != $eventEndString;
            });
        }

        // Transforme $availableSlots en un tableau de plages horaires avec début et fin
        $finalSlots = [];
        foreach ($availableSlots as $slot) {
            $slotEnd = \DateTime::createFromFormat('H:i', $slot)->modify('+30 minutes')->format('H:i');
            $finalSlots[] = ['start' => $slot, 'end' => $slotEnd];
            // dd($finalSlots);
        }
        //! Fin

        //! Récupérez la prestation et user 

        $prestation = $prestationRepository->find($id);
        //   dd($prestation);
        if (!$prestation) {
            // Gérer l'erreur si la prestation n'existe pas
            throw $this->createNotFoundException('La prestation demandée n\'existe pas.');
        }
        // dd($finalSlots);
        $reservation = new Reservation();
        $reservation->setPrestation($prestation);
        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            // aucun utilisateur n'est connecté 
            throw $this->createAccessDeniedException('Vous devez être connecté pour effectuer cette action.');

            return $this->redirectToRoute('app_login');
        }
        // set l'utilisateur à la réservation
        $reservation->setUser($user);

        //! Fin

        // cree tableau 
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->remove('prestation');
        $form->handleRequest($request);

        //verifie si il est soumis et valid
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv = $request->request->get('rdv');
            // dd($rdv);
            if ($rdv) {
                try {
                    $reservation->setRdv(new \DateTime($rdv));
                } catch (\Exception $e) {
                    return $this->json(['error' => 'Format de date invalide.'], Response::HTTP_BAD_REQUEST);
                }
                $entityManager->persist($reservation);
                $entityManager->flush();

                // dd($rdvs);
                 // Envoyer l'email de confirmation
            $this->sendReservationConfirmationEmail($mailer, $reservation);

                return $this->redirectToRoute('app_reservation_valid', [
                    'pseudo' => $reservation->getUser()->getPseudo(),
                    'prestation' => $reservation->getPrestation()->getNom(),
                    'prix' => $reservation->getPrestation()->getPrix(),
                    'rdv' => $request->request->get('rdv')
                ]);
            }
        }
        $pagination = $paginator->paginate(
            $finalSlots, // Le tableau à paginer
            $request->query->get('page', 1), // Le numéro de la page actuelle, 1 par défaut
            6// Le nombre de créneaux par page
        );
        return $this->render('reservation/new.html.twig', [
            'pagination' => $pagination,
            'selectedDate' => $selectedDate,
            'prestation' => $reservation->getPrestation()->getNom(),
            'prix' => $reservation->getPrestation()->getPrix(),
            'rdvs' => $rdvs,
            'availableSlots' => $finalSlots,
            'form' => $form->createView(),
            'prestationId' => $id,
        ]);
    }


    private function sendReservationConfirmationEmail(MailerInterface $mailer, Reservation $reservation): void
    {
        // dd($reservation);
        $email = (new Email())
            ->from('adminBarberShop@mail.com') 
            ->to($reservation->getUser()->getEmail()) 
            ->addTo('adminBarberShop@mail.com')
            ->subject('Confirmation de votre réservation')
            ->html($this->renderView(
                'reservation/emailReservation.html.twig',
                ['reservation' => $reservation]
            ));

        $mailer->send($email);
    }

    



    #[Route('/valid', name: 'app_reservation_valid')]
    public function validResa(Request $request)
    {
        $pseudo = $request->query->get('pseudo');
        $prestation = $request->query->get('prestation');
        $prix = $request->query->get('prix');
        $rdv = $request->query->get('rdv');
        //  dd($rdv);

        return $this->render('reservation/validResa.html.twig', [
            'reservationDetails' => [
                'pseudo' => $pseudo,
                'prestation' => $prestation,
                'prix' => $prix,
                'rdv' => $rdv
            ]
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
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
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


