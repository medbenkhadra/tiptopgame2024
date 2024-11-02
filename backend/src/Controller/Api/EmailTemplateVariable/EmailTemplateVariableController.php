<?php

namespace App\Controller\Api\EmailTemplateVariable;

use App\Entity\EmailService;
use App\Entity\EmailTemplate;
use App\Entity\EmailTemplateVariable;
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


class EmailTemplateVariableController extends AbstractController
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
    public function getEmailTemplateVariables(Request $request): JsonResponse
    {
        $emailTemplateVariables = $this->entityManager->getRepository(EmailTemplateVariable::class)->findAll();
        $data = [];
        foreach ($emailTemplateVariables as $emailTemplateVariable) {
            $data[] = [
                'id' => $emailTemplateVariable->getId(),
                'name' => $emailTemplateVariable->getName(),
                'label' => $emailTemplateVariable->getLabel(),
                'services' => $emailTemplateVariable->getServicesJson(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmailTemplateVariablesByService(Request $request): JsonResponse
    {
        $emailTemplateServices = $this->entityManager->getRepository(EmailService::class)->findAll();

        $data = [];
        foreach ($emailTemplateServices as $emailTemplateService) {

            $data[] = [
                'id' => $emailTemplateService->getId(),
                'name' => $emailTemplateService->getName(),
                'label' => $emailTemplateService->getLabel(),
                'description' => $emailTemplateService->getDescription(),
                'variables' => $emailTemplateService->getVariablesJson(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }



}
