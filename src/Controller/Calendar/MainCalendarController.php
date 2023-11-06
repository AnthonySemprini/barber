<?php

namespace App\Controller\Calendar;

use App\Repository\PrestationRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainCalendarController extends AbstractController
{
    
  
  
  #[Route('/main', name: 'app_main_calendar')]
  public function index(ReservationRepository $calendar,PrestationRepository $prestationRepository): Response
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
              
              
          ];
      }

      $data = json_encode($rdv);
      return $this->render('reservation/index.html.twig', [
        'data'=> $data,
        'prestations' => $prestationRepository->findAll(),
        // 'prestations' => $prestationRepository->findByOne(['id' => ]),
    ]);
    
}

#[Route('/main/resa/{id}', name: 'app_main_calendar_resa')]
public function resa(PrestationRepository $prestationRepository)
{
    return $this->render('reservation/index.html.twig',[
             'prestation' => $prestationRepository->findOneBy(['id']),
     ]);
   }
}
