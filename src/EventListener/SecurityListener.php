<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Doctrine\ORM\EntityManager;

class SecurityListener {

    protected $em;
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * onAuthenticationFailure
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure( AuthenticationFailureEvent $event )
    {
        if($event->getAuthenticationException()->getMessage() == 'Bad credentials.'){
            $username = $event->getAuthenticationToken()->getUsername();
            $repository = $this->em->getRepository('App:User');
            $user = $repository->findOneBy(['username' => $username]);
            if($user !== null){
                $user->addNbFailedConnexion();
                $this->em->persist($user);
                $this->em->flush();
            }
        }
    }

    /**
     * onAuthenticationSuccess
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        $user = $event->getAuthenticationToken()->getUser();
        $user->razNbFailedConnexion();
        $this->em->persist($user);
        $this->em->flush();

    }
}