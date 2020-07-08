<?php

namespace App\Listener;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * LogoutListener constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if ($user instanceof User) {
            $this->processingLogoutTimeStamp($user);
        }
    }

    /**
     * @return EntityManager
     */
    private function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function processingLogoutTimeStamp(User $user): void
    {
        $user->setLogoutAt(new \DateTime());

        $this->getEm()->persist($user);
        $this->getEm()->flush();
    }
}