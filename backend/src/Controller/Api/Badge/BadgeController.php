<?php

namespace App\Controller\Api\Badge;


use App\Entity\Badge;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;


class BadgeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }


    public function getAllBadges(Request $request): JsonResponse
    {
        $badges = $this->entityManager->getRepository(Badge::class)->findAll();

        $data = [];

        foreach ($badges as $badge) {
            $data[] = $badge->getBadgeJson();
        }

        return new JsonResponse([
            'badges' => $data,
        ], Response::HTTP_OK);
    }

    public function getBadgeById(Request $request, int $id): JsonResponse
    {
        $badge = $this->entityManager->getRepository(Badge::class)->find($id);

        if (!$badge) {
            return new JsonResponse(['error' => 'Badge not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($badge->getBadgeJson(), JsonResponse::HTTP_OK);
    }



    public function getClientBadges(int $id , Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $badges = $user->getBadges();

        $data = [];

        foreach ($badges as $badge) {
            $data[] = $badge->getBadgeJson();
        }

        return new JsonResponse([
            'badges' => $data,
        ], JsonResponse::HTTP_OK);
    }

}
