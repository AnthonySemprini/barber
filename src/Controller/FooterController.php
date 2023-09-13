<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    #[Route('/footer/contact', name: 'app_contact_footer')]
    public function index(): Response
    {
        return $this->render('footer/contact.html.twig');
    }
    #[Route('/footer/mention', name: 'app_mention_footer')]
    public function mention(): Response
    {
        return $this->render('footer/mention.html.twig');
    }
    #[Route('/footer/confidentialite', name: 'app_confidentialite_footer')]
    public function confidentialite(): Response
    {
        return $this->render('footer/confidentialite.html.twig');
    }
    #[Route('/footer/condition', name: 'app_condition_footer')]
    public function condition(): Response
    {
        return $this->render('footer/condition.html.twig');
    }
}
