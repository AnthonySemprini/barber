<?php

namespace App\Controller;

use App\Entity\User;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    
    #[Route('/user/{id}/delete', name:'app_user_delete')]
    public function delete(Request $request, $id, ManagerRegistry $managerRegistry):Response
    {
        $entityManager = $managerRegistry->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        //verifiez si le user existe
        if(!$user){
            throw $this->createNotFoundException('L\'utilisateur avec l\'ID '.$id. 'n\'existe pas.');
        }
        //Supprimez le user
        $entityManager->remove($user);
        $entityManager->flush();

        //Redirigez vers une page de confirmation
        return $this->redirectToRoute('app_home');
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
