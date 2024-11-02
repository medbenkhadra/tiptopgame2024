<?php

namespace App\Controller\Api\Store;

use App\Entity\ActionHistory;
use App\Entity\User;
use App\Service\User\UserService;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Role;
use App\Entity\Store;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class StoreController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    private UserService $userService;


    public function __construct(EntityManagerInterface $entityManager , UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;

    }



    /**
     * @IsGranted("ROLE_STOREMANAGER")
     */
    public function getStoreForStoreManager(int $id): JsonResponse
    {

        //get the user store
        $user = $this->getUser();
        $stores = $user->getStores();
        $storesResponse = [];
        foreach ($stores as $store) {
            $storesResponse[] =
                $store->getStoreJson();
        }

        return $this->json([
            'storesResponse' => $storesResponse,
        ], 200);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     */
    public function getStoresForAdmin(): JsonResponse
    {
        $stores = $this->entityManager->getRepository(Store::class)->findAll();
        $storesResponse = [];
        foreach ($stores as $store) {
            $storesResponse[] =
                $store->getStoreJson();
        }

        $closedStoresCount = 0;
        $openStoresCount = 0;
        foreach ($stores as $store) {
            if ($store->getStatus() == Store::STATUS_CLOSED) {
                $closedStoresCount++;
            } else {
                $openStoresCount++;
            }
        }


        return $this->json([
            'storesResponse' => $storesResponse,
            'storesCount' => count($storesResponse),
            'closedStoresCount' => $closedStoresCount,
            'openStoresCount' => $openStoresCount,
        ], 200);
    }



    public function getStoreById(int $id): JsonResponse
    {
        $store = $this->entityManager->getRepository(Store::class)->findOneBy(['id' => $id]);
        if (!$store) {
            return $this->json([
                'error' => 'Store not found'
            ], 404);
        }


        $storeUsers = $store->getUsers();
        $managerUserCount = 0;
        $employeeUserCount = 0;
        $clientUserCount = 0;
        $adminUserCount = 0;

        $playedTickets = 0;


        foreach ($storeUsers as $storeUser) {
            $playedTickets+=count($storeUser->getTickets());

            if ($storeUser->getRole()->getName() == Role::ROLE_STOREMANAGER) {
                $managerUserCount++;
            } else if ($storeUser->getRole()->getName() == Role::ROLE_EMPLOYEE) {
                $employeeUserCount++;
            } else if ($storeUser->getRole()->getName() == Role::ROLE_CLIENT) {
                $clientUserCount++;
            } else if ($storeUser->getRole()->getName() == Role::ROLE_ADMIN) {
                $adminUserCount++;
            }
        }

        return new JsonResponse([
            'storeResponse' => $store->getStoreJson(),
            'managerUserCount' => $managerUserCount,
            'employeeUserCount' => $employeeUserCount,
            'clientUserCount' => $clientUserCount,
            'playedTickets' => $playedTickets,
            'adminUserCount' => $adminUserCount,
        ]);

    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @throws Exception
     */
    public function addNewStoreByAdmin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json([
                'error' => 'No data provided'
            ], 400);
        }


        $store = new Store();
        $store->setName($data['name'] ?? "");
        $store->setAddress($data['address'] ?? "");
        $store->setCity($data['city'] ?? "");
        $store->setPostalCode($data['postal_code'] ?? "");
        $store->setCountry($data['country'] ?? "");
        $store->setStatus($data['status'] ?? Store::STATUS_OPEN);
        if ($data['opening_date'] != null && $data['opening_date'] != ''){
            $formattedDate = \DateTime::createFromFormat('d/m/Y', $data['opening_date']);
            $store->setOpeningDate($formattedDate);
        }else {
            $store->setOpeningDate(new \DateTime());
        }
        $decimalCapitalValue = number_format((float)$data['capital'], 2, '.', '');
        $store->setCapital($decimalCapitalValue);
        $store->setHeadquartersAddress($data['headquarters_address']);
        $store->setPhone($data['phone_number']);
        $store->setEmail($data['email']);
        $store->setSiren($data['siren']);


        $roleLabel = "";
        if ($this->getUser()->getRoles()[0] === Role::ROLE_ADMIN) {
            $roleLabel = "L'Administateur";
        }
        $storeName = $data['name'] ?? "";
        $details = $roleLabel. " a ajouté un nouveau magasin " .$storeName. " ( ". $store->getSiren(). " )";
        $this->userService->createActionHistory(ActionHistory::STORES_MANAGEMENT , $this->getUser() , null , $store , $details);






        $actionHistory = new ActionHistory();
        $actionHistory->setDetails($details);
        $actionHistory->setActionType(ActionHistory::STORES_MANAGEMENT);
        $actionHistory->setUserDoneAction($this->getUser());
        $actionHistory->setUserActionRelatedTo(null);
        $actionHistory->setStore($store);
        $actionHistory->setCreatedAt(new \DateTime());



        $this->entityManager->persist($actionHistory);




        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $admin) {
            $role = $admin->getRole();
            if ($role != null && $role != "") {
                if ($role->getName() == Role::ROLE_ADMIN) {
                    $admin->addStore($store);
                    $store->addUser($admin);
                    $this->entityManager->persist($store);
                    $this->entityManager->persist($admin);
                }
            }
        }





        $this->entityManager->persist($store);
        $this->entityManager->flush();

        return new JsonResponse([
            'status'=>'added',
            'storeResponse' => $store->getStoreJson(),
        ]);
    }


    /**
     * @throws Exception
     */
    public function updateStoreByIdForAdmin(int $id, Request $request): JsonResponse
    {
        $store = $this->entityManager->getRepository(Store::class)->findOneBy(['id' => $id]);
        if (!$store) {
            return $this->json([
                'error' => 'Store not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        $requiredFields = ['name', 'address', 'city', 'postal_code', 'country', 'status', 'capital', 'headquarters_address', 'phone_number', 'email', 'siren'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->json([
                    'error' => 'Missing field: ' . $field
                ], 400);
            }
        }

        $store->setName($data['name']);
        $store->setAddress($data['address']);
        $store->setCity($data['city']);
        $store->setPostalCode($data['postal_code']);
        $store->setCountry($data['country']);
        $store->setStatus($data['status']);

        if ($data['opening_date'] != null && $data['opening_date'] != ''){
            $formattedDate = \DateTime::createFromFormat('d/m/Y', $data['opening_date']);
            $store->setOpeningDate($formattedDate);
        }

        $decimalCapitalValue = number_format((float)$data['capital'], 2, '.', '');
        $store->setCapital($decimalCapitalValue);
        $store->setHeadquartersAddress($data['headquarters_address']);
        $store->setPhone($data['phone_number']);
        $store->setEmail($data['email']);
        $store->setSiren($data['siren']);



        $loggedUser = $this->getUser();
        $roleLabel = "";
        if ($loggedUser->getRoles()[0] === Role::ROLE_ADMIN) {
            $roleLabel = "L'Administateur";
        } else if ($loggedUser->getRoles()[0] == Role::ROLE_STOREMANAGER) {
            $roleLabel = "Le Manager";
        }
        $storeName = $store->getName();
        $details = $roleLabel. " a modifié le magasin ." .$storeName. " ( ". $store->getSiren(). " )";
        $this->entityManager->persist($store);

        $this->userService->createActionHistory(ActionHistory::STORES_MANAGEMENT , $this->getUser() , null , $store  , $details);



        return new JsonResponse([
            'status'=>'updated',
            //'storeResponse' => $store->getStoreJson(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteStoreById(int $id): JsonResponse
    {
        $store = $this->entityManager->getRepository(Store::class)->findOneBy(['id' => $id]);
        if (!$store) {
            return $this->json([
                'error' => 'Store not found'
            ], 404);
        }

        $users = $store->getUsers();
        $usersHorsAdmin = [];
        foreach ($users as $user) {
            if ($user->getRole()->getName() != Role::ROLE_ADMIN) {
                $usersHorsAdmin[] = $user;
            }
        }
        if (count($usersHorsAdmin) > 0) {
            return $this->json([
                'error' => 'Store has users'
            ], 400);
        }

        $this->entityManager->remove($store);
        $this->entityManager->flush();

        return new JsonResponse([
            'status'=>'deleted',
            'storeResponse' => $store->getStoreJson(),
        ]);
    }




    /**
     * @IsGranted("ROLE_CLIENT")
     */
    public function getStoresForClient(Request $request): JsonResponse
    {
        $stores = $this->entityManager->getRepository(Store::class)->findAll();
        $totalStoresCount = count($stores);


        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 9);
        $offset = ($page - 1 ) * $pageSize;
        $search = $request->query->get('search', "");





        $storeRepository = $this->entityManager->getRepository(Store::class);
        $query = $storeRepository->createQueryBuilder('s');

        if($search != "") {
            $query->where('s.name LIKE :search')
                ->orWhere('s.address LIKE :search')
                ->orWhere('s.city LIKE :search')
                ->orWhere('s.postal_code LIKE :search')
                ->orWhere('s.country LIKE :search')
                ->orWhere('s.phone LIKE :search')
                ->orWhere('s.email LIKE :search')
                ->orWhere('s.siren LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        $query->orderBy('s.id', 'ASC');

        $totalStoresCount = count($query->getQuery()->getResult());

        $query->setMaxResults($pageSize)
            ->setFirstResult($offset)
            ->getQuery();


        $storesResult = $query->getQuery()->getResult();

        $storesResponse = [];
        foreach ($storesResult as $store) {
            $storesResponse[] = $store->getStoreJson();
        }
        return $this->json([
            'storesResponse' => $storesResponse,
            'totalStoresCount' => $totalStoresCount,
        ], 200);
    }

    //associateClientToStore
    /**
     * @IsGranted("ROLE_CLIENT")
     */
    public function associateClientToStore(Request $request): JsonResponse {
            $data = json_decode($request->getContent(), true);
            $id = $data['storeId'] ?? null;
            $store = $this->entityManager->getRepository(Store::class)->findOneBy(['id' => $id]);

            if (!$store) {
                return $this->json([
                    'error' => 'Store not found'
                ], 404);
            }
            $user = $this->getUser();
            $user->addStore($store);
            $store->addUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($store);
            $this->entityManager->flush();

            return new JsonResponse([
                'status'=>'associated',
            ]);

    }

}
