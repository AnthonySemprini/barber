<?php

namespace App\Controller\Calendar;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class MainCalendarController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ReservationRepository $reservationRepository): Response
    {
      $events = $reservationRepository->findAll();
        // dd($events);
        

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'nom' => $event->getNom(),
                'prenom' => $event->getPrenom(),
                'numTel' => $event->getNumTel(),
                'rdv' => $event->getRdv()->format('Y-m-d H:i:s'),
                'prestation' => $event->getPrestation(),
                'textColor' => $event->getTexteColor(),
                'user' => $event->getUser()
            ];
        }
        $data = Json_encode($rdvs);

        //redirige vers la page reservation
        return $this->render('reservation/index.html.twig', [
            'data' => $data,
        ]);
}
}