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
        $calendar = new FullCalendar();   

        foreach($events as $event){
            $calendar->addEvent(
                new FullCalendarEvent(
                $event->getId(),
                $event->getNom(),
                $event->getPrenom(),
                $event->getNumTel(),
                $event->getRdv()->format('Y-m-d H:i:s'),
                $event->getPrestation(),
                $event->getTexteColor(),
                $event->getUser()
                )
            );
        }
  // Render the calendar
  echo $calendar->render();

  // Return the response
  return new Response();
}
}