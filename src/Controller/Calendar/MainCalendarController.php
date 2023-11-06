<?php

namespace App\Controller\Calendar;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class MainCalendarController extends AbstractController
{
    
  
  
  #[Route('/main', name: 'app_main_calendar')]
  public function index(ReservationRepository $calendar): Response
  {
      $events = $calendar->findAll();
      //dd($events);
      foreach($events as $event){
          $rdv[] = [
              'id' => $event->getId(),
              'nom' => $event->getNom(),
              'prenom' => $event->getPrenom(),
              'numTel' => $event->getNumTel(),
              'rdv' => $event->getRdv()->format('Y-m-d H:i:s'),
              'prestation' => $event->getPrestation(),
              'user' => $event->getUser(),
              
          ];
      }

      $data = json_encode($rdv);
      return $this->render('reservation/index.html.twig', compact('data'));
      
  }
}
