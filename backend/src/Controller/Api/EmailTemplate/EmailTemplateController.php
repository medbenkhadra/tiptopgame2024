<?php

namespace App\Controller\Api\EmailTemplate;

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


class EmailTemplateController extends AbstractController
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
    public function getEmailTemplates(Request $request): JsonResponse
    {
        $emailTemplates = $this->entityManager->getRepository(EmailTemplate::class)->findAll();
        $data = [];
        foreach ($emailTemplates as $emailTemplate) {
            $data[] = [
                'id' => $emailTemplate->getId(),
                'title' => $emailTemplate->getTitle(),
                'subject' => $emailTemplate->getSubject(),
                'content' => $emailTemplate->getContent(),
                'type' => $emailTemplate->getType(),
                'service' => $emailTemplate->getServiceJson(),
                'required' => $emailTemplate->getRequired(),
                'description' => $emailTemplate->getDescription(),
                'name' => $emailTemplate->getName(),
                'variables' => $emailTemplate->getService()->getVariablesJson(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }



    /**
     * @IsGranted("ROLE_ADMIN")
     * @param $id
     * @return JsonResponse
     */
    public function getEmailTemplateById($id): JsonResponse
    {
        $emailTemplate = $this->entityManager->getRepository(EmailTemplate::class)->find($id);
        if (!$emailTemplate) {
            return new JsonResponse(['status' => 'EmailTemplate not found'], Response::HTTP_NOT_FOUND);
        }
        $data = [
            'id' => $emailTemplate->getId(),
            'title' => $emailTemplate->getTitle(),
            'subject' => $emailTemplate->getSubject(),
            'content' => $emailTemplate->getContent(),
            'type' => $emailTemplate->getType(),
            'service' => $emailTemplate->getService()->getId(),
            'required' => $emailTemplate->getRequired(),
            'description' => $emailTemplate->getDescription(),
            'name' => $emailTemplate->getName(),
            'variables' => $emailTemplate->getService()->getVariablesJson(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }




/**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function createEmailTemplate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $service = $data['service'];

        $serviceEntity = $this->entityManager->getRepository(EmailService::class)->find($service);




        $template = new EmailTemplate();
        $template->setTitle($data['title'] ?? '');
        $template->setName($data['name'] ?? '');
        $template->setDescription($data['description'] ?? '');
        $template->setType($data['type'] ?? '');
        $template->setService($serviceEntity);
        $template->setRequired(true);


        $this->entityManager->persist($template);
        $this->entityManager->flush();

        return $this->json(['message' => 'Template created successfully'], Response::HTTP_CREATED);
    }


/**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateEmailTemplateById(Request $request): JsonResponse
    {
        $template = $this->entityManager->getRepository(EmailTemplate::class)->find($request->get('id'));
        if (!$template) {
            return new JsonResponse(['status' => 'Template not found'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);



        $service = $data['service'];


        $serviceEntity = $this->entityManager->getRepository(EmailService::class)->find($service);


        $template->setTitle($data['title'] ?? '');
        $template->setName($data['name'] ?? '');
        $template->setDescription($data['description'] ?? '');
        $template->setType($data['type'] ?? '');
        $template->setService($serviceEntity);
        $template->setSubject($data['subject'] ?? '');
        $template->setContent($data['content'] ?? '');
        $template->setRequired($data['required'] ?? true);

        $statusCode = 200;
        $message = "Template a été mis à jour avec succès";

        $templateService = $template->getService();
        $templatesWithThisService = $this->entityManager->getRepository(EmailTemplate::class)->findBy(['service' => $templateService]);

        if (count($templatesWithThisService) > 0) {
            foreach ($templatesWithThisService as $templateWithThisService) {
                if ($templateWithThisService->getId() != $template->getId() && $templateWithThisService->getRequired()) {
                    $statusCode = 201;
                    $message = "Template a été mis à jour avec succès. Il y a d'autres templates avec ce service, ils ont été mis à jour pour être non requis";
                    $templateWithThisService->setRequired(false);
                    $this->entityManager->persist($templateWithThisService);
                }
            }
        }






        $this->entityManager->persist($template);
        $this->entityManager->flush();

        return $this->json(['message' => $message , 'statusCode' => $statusCode], Response::HTTP_OK);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTemplate(Request $request): JsonResponse
    {
        $template = $this->entityManager->getRepository(EmailTemplate::class)->find($request->get('id'));
        if (!$template) {
            return new JsonResponse(['status' => 'Template not found'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($template);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Template deleted'], Response::HTTP_NO_CONTENT);
    }



}
