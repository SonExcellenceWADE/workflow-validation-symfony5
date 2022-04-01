<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowSubscriber implements EventSubscriberInterface
{

    private $mailerInterface;
    private $userRepository;
    
    public function __construct(MailerInterface $mailerInterface, UserRepository $userRepository )
    {
        $this->mailerInterface = $mailerInterface;
        $this->userRepository = $userRepository;
    }


    public function onWorkflowDemandeLeave(Event $event)
    {
        //Retrieves All users for sending email
        //Event sending Email for User by Son Excellence WADE
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $email = (new Email())
            ->from($event->getSubject()->getUser()->getEmail())
            ->to($user->getEmail())
            ->addTo($user->getEmail())
            ->subject('Demande de jouet - ' .$event->getSubject()->getName())
            ->text('Bonjour Maman et Papa, merci de me commander le jouet :  '.$event->getSubject()->getName());
            $this->mailerInterface->send($email);
        }

    }

        public function onWorkflowDemandeReceived(Event $event)
        {
            //Retrieves All users for sending email
            //Event sending Email for User by Son Excellence WADE
           
            $email = (new Email())

            ->from('sonexcellence.wade@lgmail.fr')
            ->to($event->getSubject()->getUser()->getEmail())
            ->subject('Ton jouet est la, oh oh oh !')
            ->text('Ton jouet est arrivé, amuse toi bien !');
    
                $this->mailerInterface->send($email);
            }
        
    
    
    public static function getSubscribedEvents()
    {
        return [
            'workflow.demande.leave.request' => 'onWorkflowDemandeLeave',
            'workflow.demande.entered.received' => 'onWorkflowDemandeReceived'
        ];
    }
}
