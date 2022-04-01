<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DemandeController extends AbstractController
{
    const SUCCESS = 'success';
    const FORM = 'form';
    const DEMANDES = 'demandes';
    const APP_DEMANDE = 'app_demande';
    const APP_PARENT = 'app_parent';

    private $demandeWorkflow;
    private $mailerInterface;
    private $userRepository;

public function __construct(WorkflowInterface $demandeWorkflow, MailerInterface $mailerInterface, UserRepository $userRepository)
{
   $this->demandeWorkflow = $demandeWorkflow;
   $this->mailerInterface = $mailerInterface;
   $this->userRepository = $userRepository;
}
    /**
     * @Route("/demande", name="app_demande")
     * @IsGranted("ROLE_KID", statusCode=401, message="You are not authorized to access this page")
     */
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $demande = new Demande();

        $demande->setUser($this->getUser());

        $form = $this->createForm(DemandeType::class, $demande);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $demande = $form->getData(); 
            try {
            $this->demandeWorkflow->apply($demande, 'to_pending');
            } catch (LogicException $exception) {
                # code...
            }

            $entityManagerInterface->persist($demande);
            $entityManagerInterface->flush();

            $this->addFlash(self::SUCCESS, 'Demande enregistrée !');

            return $this->redirectToRoute(self::APP_DEMANDE);

        }


        
        return $this->render('demande/index.html.twig', [
           self::FORM => $form->createView()
        ]);
    }


    /**
     * @Route("/parent", name="app_parent")
     * @IsGranted("ROLE_PARENT", statusCode=401, message="You are not authorized to access this page")
     */
    public function parent(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/parent.html.twig', [
            self::DEMANDES => $demandeRepository->findAll()
        ]);
    }



    /**
     * @Route("/change/{id}/{to}", name="app_change")
     */
    public function change(Demande $demande, String $to, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->demandeWorkflow->apply($demande, $to);
        } catch (LogicException $exception) {
            //
        }

        $entityManager->persist($demande);
        $entityManager->flush();

        $this->addFlash(self::SUCCESS, 'Action Enregistrée !');

        return $this->redirectToRoute(self::APP_PARENT);
    }


}
