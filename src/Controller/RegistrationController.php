<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppMyAuthAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;

    /**
     * RegistrationController constructor.
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     */
    public function __construct(GuardAuthenticatorHandler $guardAuthenticatorHandler)
    {
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        AppMyAuthAuthenticator $appMyAuthAuthenticator
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->guardAuthenticatorHandler
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $appMyAuthAuthenticator,
                    'main'
                );

//            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
