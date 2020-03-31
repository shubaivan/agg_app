<?php


namespace App\Controller\Rest;

use App\Entity\Models\SentEmail;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class EmailsController extends AbstractRestController
{
    /**
     * sent Email.
     *
     * @Rest\Post("/api/email/send")
     * @ParamConverter("sentEmail", converter="fos_rest.request_body")
     *
     * @Operation(
     *     tags={"Email"},
     *     summary="sent Email",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @Model(type=App\Entity\Models\SentEmail::class)
     *
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="email was sent",
     *         @Model(type=SentEmail::class)
     *     )
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_OK)
     *
     * @return SentEmail|ConstraintViolationListInterface
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCategoriesAction(
        SentEmail $sentEmail,
        ConstraintViolationListInterface $validationErrors,
        \Swift_Mailer $mailer
    )
    {
        if (count($validationErrors) > 0) {
            // Handle validation errors
            return $validationErrors;
        }
        $supportEmail = $this->getParameter('support_email');
        $message = (new \Swift_Message('Collaboration opportunity'))
            ->setFrom($sentEmail->getEmail(), 'Collaboration')
            ->setTo($supportEmail, 'Support')
            ->setBody(
                $this->renderView(
                    'emails/support.html.twig',
                    [
                        'sentEmail' => $sentEmail
                    ]
                ),
                'text/html'
            );

        $send = $mailer->send($message);

        return $sentEmail;
    }
}