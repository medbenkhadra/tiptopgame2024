<?php

namespace App\Controller\Api\EmailService;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\TicketHistory;
use App\Entity\User;
use Exception;
use App\Entity\Role;
use App\Entity\Store;
use App\Entity\Ticket;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class EmailServiceController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmailServices(Request $request): JsonResponse
    {
        $emailServices = $this->entityManager->getRepository(EmailService::class)->findAll();

        $data = [];

        foreach ($emailServices as $emailService) {
            $data[] = [
                'id' => $emailService->getId(),
                'name' => $emailService->getName(),
                'label' => $emailService->getLabel(),
                'description' => $emailService->getDescription(),
                'templates' => $emailService->getTemplatesJson(),

            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }




}
