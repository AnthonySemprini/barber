<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrestationController extends AbstractController
{
    #[Route('/prestation', name: 'app_prestation')]
    public function index(PrestationRepository $prestationRepository): Response
    {
            return $this->render('prestation/index.html.twig', [
                'prestations' => $prestationRepository->findBy([],
                ['nom' => 'ASC'])
            ]);
    }
}
