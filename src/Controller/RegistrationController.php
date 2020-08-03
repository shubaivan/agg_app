<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppMyAuthAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $render = [];
        try {
            $this->denyAccessUnlessGranted(User::ROLE_SUPER_ADMIN);

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
                $user->setRoles([User::ROLE_ADMIN]);
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
            $render['registrationForm'] = $form->createView();
        } catch (\Exception $e) {
            $this->addFlash(
                'warning',
                $e->getMessage()
            );
        }

        return $this->render('registration/register.html.twig', $render);
    }
}
