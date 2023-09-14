<?php

namespace App\Controller;

use App\Repository\PrestationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrestationController extends AbstractController
{
    #[Route('/prestation', name: 'app_prestation')]
    public function index(PrestationRepository $prestationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $prestationRepository->paginationQuery(),
            $request->query->get('page', 1),
            6
        );

            return $this->render('prestation/index.html.twig', [
                'pagination' => $pagination
            ]);
    }
}
