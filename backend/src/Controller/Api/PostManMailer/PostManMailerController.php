<?php

namespace App\Controller\Api\PostManMailer;

use App\Entity\EmailService;
use App\Entity\Prize;
use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\User;
use App\Service\Mailer\PostManMailerService;
use DateTime;
use PHPUnit\Exception;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class PostManMailerController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    private PostManMailerService $postManMailerService;



    public function __construct(EntityManagerInterface $entityManager , PostManMailerService $postManMailerService )
    {
        $this->entityManager = $entityManager;
        $this->postManMailerService = $postManMailerService;

    }

    /**
     * @throws SyntaxError
     * @throws RandomException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendActivationEmail(int $id , Request $request): JsonResponse
    {
        $receiver = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        $activationEmailServiceClient =EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_CLIENT ;
        $activationEmailServiceEmployee = EmailService::EMAILSERVICE_ACCOUNT_ACTIVATION_EMPLOYEE ;

        $receiverRole = $receiver ? $receiver->getRoles()[0] : null;

        if ($receiverRole == Role::ROLE_CLIENT) {
            $finalService = $activationEmailServiceClient;
        } else {
            $finalService = $activationEmailServiceEmployee;
        }

        $activationToken = null;
        if($receiver){
            $activationToken = bin2hex(random_bytes(32));
            $receiver->setToken($activationToken);
            $receiver->setTokenExpiredAt((new \DateTime())->modify('+1 day'));

            $this->entityManager->persist($receiver);
            $this->entityManager->flush();
        }


        try {
            if ($this->postManMailerService->sendEmailTemplate($finalService, $receiver, [
                'token' => $activationToken,
                'ticket' => null,
            ])) {
                return new JsonResponse('Activation Email sent successfully!', 200);
            } else {
                return new JsonResponse('Activation Email not sent!', 500);
            }
        } catch (\Exception $e) {
            return new JsonResponse('Error sending activation email: ' . $e->getMessage(), 500);
        }


    }


    public function checkClientActivationTokenValidity(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $token = $data['token'];
        $linkStatus = $this->entityManager->getRepository(User::class)->checkClientActivationTokenValidity($email,$token);

        if ($linkStatus) {
            $this->entityManager->getRepository(User::class)->activateUserAccount($email);
            return new JsonResponse('Token is valid' , 200);
        } else {
            return new JsonResponse('Token is not valid', 500);
        }
    }

}
