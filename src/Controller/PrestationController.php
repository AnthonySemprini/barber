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
        // Utilisation de knpPaginator pour paginer les resultat de la requete prestation
        $pagination = $paginator->paginate(
            $prestationRepository->paginationQuery(),
            $request->query->get('page', 1),
            8 //Nombre d'elements par page
        );

            return $this->render('prestation/index.html.twig', [
                'pagination' => $pagination
            ]);
    }
}
