<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeFormType;
use App\Entity\ProduitCommande;
use Doctrine\ORM\EntityManager;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CommandeController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/commande/confirmation', name: 'app_commande_confirmation')]
    public function confirmationCommande(EntityManagerInterface $entityManager, Request $request, SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle 'USER'. Si ce n'est pas le cas, l'accès à cette fonction est refusé.
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupère le panier de la session
        $panier = $session->get('panier', []);
// dd($panier);
        // Récupère l'utilisateur actuellement connecté.
        $user = $this->getUser();

        // Crée un nouvel objet DateTime
        $now = new \DateTime();

        // Crée une nouvelle instance de la classe Commande.
        $commande = new Commande();
        // Attribue l'utilisateur connecté à la commande.
        $commande->setUser($user);
        // Définit la date de la commande 
        $commande->setDateCommande($now);
        // Crée un formulaire pour l'objet Commande.
        $form = $this->createForm(CommandeFormType::class, $commande);
        // Gère la requête HTTP actuelle et initialise le formulaire.
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->managerRegistry->getManager();
            // Persiste et enregistre l'objet Commande dans la base de données.
            $entityManager->persist($commande);
            $entityManager->flush();


            // Parcourt chaque élément du panier.
            foreach ($panier as $prod => $qtt) {
                // Crée une nouvelle instance de ProduitCommande.
                $produit = $produitRepository->find($prod);
                $produitCommande = new ProduitCommande();
                // Attribue le produit et la quantité à ProduitCommande.
                $produitCommande->setProduit($produit);
                $produitCommande->setQuantite($qtt);
                $produitCommande->setCommande($commande);
                // Persiste et enregistre l'objet ProduitCommande dans la base de données.
                // dd($produitCommande);
                $entityManager->persist($produitCommande);
                $entityManager->flush();
            }
            
            // Vider le panier de la session
            $session->set('panier', []);

            // Redirige l'utilisateur vers la page de paiement de la commande.
            return $this->redirectToRoute('app_commande_confirm', ['id' => $commande->getId()]);
        }

        // Si le panier est vide, redirige l'utilisateur vers la page d'accueil.
        if ($panier === []) {
            return $this->redirectToRoute('app_home');
        }

        // Rendu de la vue avec le formulaire de commande.
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'commandeForm' => $form->createView()
        ]);
    }

    #[Route('/commande/confirm/{id}', name: 'app_commande_confirm')]
    public function confirm($id, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            // Gérer l'erreur si la commande n'existe pas
            throw $this->createNotFoundException('La commande n\'a pas été trouvée.');
        }
        $total = 0;
        foreach ($commande->getProduitCommandes() as $panier) {
            $total += $panier->getProduit()->getPrix() * $panier->getQuantite();
        }

         // Envoyer l'email de confirmation ici
         $this->sendOrderConfirmationEmail($mailer, $commande);

        return $this->render('commande/valid.html.twig', [
            'commande' => $commande, // données à passer au template
            'total' => $total
        ]);
    }

    private function sendOrderConfirmationEmail(MailerInterface $mailer, Commande $commande): void
    {
        $total = 0;
        foreach ($commande->getProduitCommandes() as $panier) {
            $total += $panier->getProduit()->getPrix() * $panier->getQuantite();
        } 

        $email = (new Email())
            ->from('adminBarberShop@mail.com') // Remplacez par votre adresse email
            ->to($commande->getUser()->getEmail())
            ->addTo('adminBarberShop@mail.com')
            ->subject('Confirmation de votre commande')
            ->html($this->renderView(
                'commande/emailConfirmation.html.twig',[
                    'commande' => $commande,
                    'total' => $total
                ]
            ));


        $mailer->send($email);
    }

    #[Route('/commande/pdf/{id}', name: 'app_commande_pdf')]
    public function generatePdf($id, EntityManagerInterface $entityManager): Response
    {
       
        $commande = $entityManager->getRepository(Commande::class)->find($id);
      

// DD($commande);
        if (!$commande) {
            // Gérer l'erreur si la commande n'existe pas
            throw $this->createNotFoundException('La commande n\'a pas été trouvée.');
        }
        $total = 0;
        foreach ($commande->getProduitCommandes() as $panier) {
            $total += $panier->getProduit()->getPrix() * $panier->getQuantite();
        }
        // dd($total);
        // Récupérez le HTML généré par votre template Symfony
        $html = $this->renderView('commande/fichePdf.html.twig', [
            'commande' => $commande, // données à passer au template
            'total' => $total,
        ]);

      

        // Configurez Dompdf selon vos besoins
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instanciez Dompdf avec nos options
        $dompdf = new Dompdf($pdfOptions);
        // Chargez le HTML dans Dompdf
        $dompdf->loadHtml($html);
        // (Optionnel) Configurez le format et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');
        // Rendu du PDF
        $dompdf->render();
        
              // Envoyer le PDF au navigateur
              $pdfOutput = $dompdf->output();
              $response = new Response($pdfOutput);
              $response->headers->set('Content-Type', 'application/pdf');
              $response->headers->set('Content-Disposition', 'attachment; filename="commande_' . $id . '.pdf"');
      
              return $response;
    }
}