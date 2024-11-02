<?php

namespace App\Controller\Api\User;

use App\Entity\ActionHistory;
use App\Entity\Avatar;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\UserPersonalInfo;
use App\Service\User\UserService;
use DateTime;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Role;
use App\Entity\Store;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;



    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordEncoder;
    private UserService $userService;


    public function __construct(EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordEncoder , UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userService = $userService;

    }

    public function getUserProfileById(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        return new JsonResponse([
            'user' => $user->getUserJson()
        ]);
    }

    public function updateUserProfileById(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $user->setFirstName($data['firstname']);
        $user->setLastName($data['lastname']);

        if($data['email'] != $user->getEmail()){
            $oldUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($oldUser) {
                return $this->json([
                    'error' => 'Email already exists'
                ], 400);
            }
            $user->setEmail($data['email']);
        }



        $user->setPhone($data['phone']);
        $user->setStatus($data['status']);
        $user->setGender($data['gender']);



        $roleLabel = "";
        if ($this->getUser()->getRoles()[0] === Role::ROLE_ADMIN) {
            $roleLabel = "L'Administateur";
        } else if ($this->getUser()->getRoles()[0] == Role::ROLE_STOREMANAGER) {
            $roleLabel = "Le Manager";
        }



        $userRole=$user->getRoles()[0];
        $accountLabel="";
        if($userRole==Role::ROLE_STOREMANAGER) {
            $accountLabel = "Manager";
        }else if($userRole==Role::ROLE_EMPLOYEE) {
            $accountLabel = "Employé";
        }
        $store = $user->getStores()[0] ?? null;
        $storeName = $store ? $store->getName() : "N/A";
        $details = $roleLabel. " a modifié le profil de ".$accountLabel." ".$user->getFullName()." du magasin ".$storeName;


        $this->userService->createActionHistory(ActionHistory::USERS_MANAGEMENT , $this->getUser() , null , $store , $details);


        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'updated',
            'user' => $user->getUserJson()
        ]);
    }


    public function getClients(Request $request): JsonResponse
    {


        $firstname =  $request->get('firstname' , null);
        $lastname =  $request->get('lastname' , null);
        $status =  $request->get('status' , null);
        $store =  $request->get('store' , null);
        $page = $request->get('page' , 1);
        $limit = $request->get('limit' , 10);
        $email =  $request->get('email' , null);
        $sexe =  $request->get('genre' , null);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.role', 'ur')
            ->where('ur.name = :role')
            ->setParameter('role', 'ROLE_CLIENT');



        if ($firstname != "" && $firstname != null) {
            $qb->andWhere('u.firstname LIKE :firstname')
                ->setParameter('firstname', '%' . $firstname . '%');
        }

        if ($lastname != "" && $lastname != null) {
            $qb->andWhere('u.lastname LIKE :lastname')
                ->setParameter('lastname', '%' . $lastname . '%');
        }

        if ($status != "" && $status != null) {
            $qb->andWhere('u.status = :status')
                ->setParameter('status', $status);
        }

        if ($store != "" && $store != null) {
            $qb->innerJoin('u.stores', 's')
                ->andWhere('s.id = :store')
                ->setParameter('store', $store);
        }

        if ($email != "" && $email != null) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . trim($email) . '%');
        }

        if ($sexe != "" && $sexe != null) {
            $qb->andWhere('LOWER(u.gender) LIKE :gender')
                ->setParameter('gender', '%' . strtolower(trim($sexe)) . '%');
        }

        $userRole = $this->getUser()->getRoles()[0];
        if($userRole == Role::ROLE_STOREMANAGER){
            $qb->innerJoin('u.stores', 's')
                ->andWhere('s.id = :store')
                ->setParameter('store', $this->getUser()->getStores()[0]->getId());
        }


        $totalCount = count($qb->getQuery()->getResult());


        $page = $page ?? 1;
        $pageSize = $limit ?? 10;
        $qb->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);


        $users = $qb->getQuery()->getResult();



        $usersJson = [];
        foreach ($users as $user) {
            $usersJson[] =
                $user->getUserJson();
        }


        return $this->json([
            'users' => $usersJson,
            'totalCount' => $totalCount,
            'resultCount' => count($users),
            'status' => 'success',
        ]);
    }




    public function getStoreClients(int $id): JsonResponse
    {
        $store = $this->entityManager->getRepository(Store::class)->findOneBy(['id' => $id]);
        if (!$store) {
            return $this->json([
                'error' => 'Store not found'
            ], 404);
        }

        $users = $store->getUsers();

        $usersJson = [];
        foreach ($users as $user) {
            $userRole = $user->getRoles()[0];
            if ($userRole == Role::ROLE_CLIENT) {
                $usersJson[] =
                    $user->getUserJson();
            }
        }

        return $this->json([
            'users' => $usersJson,
            'status' => 'success',
        ]);
    }





    public function getParticipants(Request $request): JsonResponse
    {
        $firstname = $request->get('firstname', null);
        $lastname = $request->get('lastname', null);
        $status = $request->get('status', null);
        $store = $request->get('store', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $email = $request->get('email', null);
        $sexe = $request->get('genre', null);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.tickets', 't')
            ->innerJoin('u.role', 'ur')
            ->where('ur.name = :role')
            ->setParameter('role', 'ROLE_CLIENT');

        if ($firstname !== null && $firstname !== "") {
            $qb->andWhere('u.firstname LIKE :firstname')
                ->setParameter('firstname', '%' . $firstname . '%');
        }

        if ($lastname !== null && $lastname !== "") {
            $qb->andWhere('u.lastname LIKE :lastname')
                ->setParameter('lastname', '%' . $lastname . '%');
        }

        if ($status !== null && $status !== "") {
            $qb->andWhere('u.status = :status')
                ->setParameter('status', $status);
        }

        if ($store !== null && $store !== "") {
            $qb->innerJoin('u.stores', 's')
                ->andWhere('s.id = :store')
                ->setParameter('store', $store);
        }

        if ($email !== null && $email !== "") {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . trim($email) . '%');
        }

        if ($sexe !== null && $sexe !== "") {
            $qb->andWhere('LOWER(u.gender) LIKE :gender')
                ->setParameter('gender', '%' . strtolower(trim($sexe)) . '%');
        }

        $userRole = $this->getUser()->getRoles()[0];
        if ($userRole == Role::ROLE_STOREMANAGER) {
            $qb->innerJoin('u.stores', 's')
                ->andWhere('s.id = :store')
                ->setParameter('store', $this->getUser()->getStores()[0]->getId());
        }

        $qbAux = clone $qb;
        $totalCount = count($qbAux->getQuery()->getResult());

        $page = $page ?? 1;
        $pageSize = $limit ?? 10;
        $paginator = new ORMPaginator($qb);

        $totalCount = count($paginator);

        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $users = $paginator->getIterator()->getArrayCopy();

        $usersJson = [];
        foreach ($users as $user) {
            $usersJson[] =
                $user->getUserJson();
        }

        return $this->json([
            'users' => $usersJson,
            'totalCount' => $totalCount,
            'resultCount' => count($users),
            'status' => 'success',
        ]);
    }


    public function getParticipantsList(Request $request): JsonResponse
    {
        $store = $request->get('store');
        $employee = $request->get('employee');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.role', 'ur')
            ->where('ur.name = :role')
            ->setParameter('role', 'ROLE_CLIENT');

        $user = $this->getUser();
        $userRole = null;
        if($user){
            $userRole = $user->getRoles()[0];
        }

        if($store || $employee || $userRole === Role::ROLE_STOREMANAGER || $userRole === Role::ROLE_EMPLOYEE ){
            $qb->innerJoin('u.stores', 'store');
        }



        if ($store !== null) {
            $qb
                ->andWhere('store.id = :store_id')
                ->setParameter('store_id', $store);
        }

        if ($employee !== null) {
            $qb
                ->innerJoin('store.users', 'employee')
                ->andWhere('employee.id = :employee_id')
                ->setParameter('employee_id', $employee);
        }

        $userRole = $this->getUser()->getRoles()[0];
        if ($userRole === Role::ROLE_STOREMANAGER || $userRole === Role::ROLE_EMPLOYEE) {
            $storeIds = array_map(function($store) {
                return $store->getId();
            }, $this->getUser()->getStores()->toArray());

            $qb
                ->andWhere('store.id IN (:store_ids)')
                ->setParameter('store_ids', $storeIds);
        }

        $users = $qb->getQuery()->getResult();

        $usersJson = [];
        foreach ($users as $user) {
            $usersJson[] = $user->getUserJson();
        }

        return $this->json([
            'users' => $usersJson,
        ]);
    }



    public function getEmployeesList(Request $request): JsonResponse
    {

        $store =  $request->get('store' , null);
        $client =  $request->get('client' , null);

        $qb = $this->entityManager->createQueryBuilder('u');
        $qb->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.role', 'ur')
            ->where('ur.name = :role')
            ->setParameter('role', 'ROLE_EMPLOYEE');

        if ($store != "" && $store != null) {
            $qb->innerJoin('u.stores', 's')
                ->andWhere('s.id = :store')
                ->setParameter('store', $store);
        }

        if ($client != "" && $client != null) {
            $qb->leftJoin('u.tickets', 't')
                ->andWhere('t.user = :client')
                ->setParameter('client', $client);
        }


        $users = $qb->getQuery()->getResult();

        $usersJson = [];
        foreach ($users as $user) {
            $usersJson[] =
                $user->getUserJson();
        }

        return $this->json([
            'users' => $usersJson,
            'status' => 'success',
        ]);

    }




    public function getUserPersonalInfoById(int $id): JsonResponse
    {

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        return new JsonResponse([
            'user' => $user->getUserJson()
        ]);
    }


    public function updateUserProfileInfo(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $firstname= $data['firstname'];
        $lastname= $data['lastname'];
        $phone= $data['phone'];
        $address= $data['address'];
        $postal_code= $data['postalCode'];
        $city= $data['city'];
        $country= $data['country'];


        $user->setFirstName($firstname);
        $user->setLastName($lastname);
        $user->setPhone($phone);

        $userPersonalInfo = $user->getUserPersonalInfo();
        if(!$userPersonalInfo){
            $userPersonalInfo = new UserPersonalInfo();
            $userPersonalInfo->setUser($user);
            $userPersonalInfo->setAddress($address);
            $userPersonalInfo->setPostalCode($postal_code);
            $userPersonalInfo->setCity($city);
            $userPersonalInfo->setCountry($country);
            $this->entityManager->persist($userPersonalInfo);
            $this->entityManager->flush();
        }else {
            $user->getUserPersonalInfo()->setAddress($address);
            $user->getUserPersonalInfo()->setPostalCode($postal_code);
            $user->getUserPersonalInfo()->setCity($city);
            $user->getUserPersonalInfo()->setCountry($country);
        }





        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'updated',
            'user' => $user->getUserJson()
        ]);
    }


    public function updateUserAvatar($id, Request $request)
    {
        $user = $this->getUser();
        $userAvatar = $user->getAvatar();

        if ($request->files->has('avatar_file')) {
            $file = $request->files->get('avatar_file');

            if ($file->isValid()) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move($this->getParameter('avatars_upload'), $fileName);

                $path = '/avatars';

                if ($userAvatar) {
                    $this->deleteAvatarFile($userAvatar->getPath());
                } else {
                    $userAvatar = new Avatar();
                    $userAvatar->setUser($user);
                }

                $userAvatar->setFilename($fileName);
                $userAvatar->setPath($path);

                $user->setAvatar($userAvatar);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return new JsonResponse([
                    'status' => 'success',
                    'avatar' => $userAvatar->getAvatarJson(),
                ]);
            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'File upload failed',
                ], 500);
            }
        } else {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No file uploaded',
            ], 400);
        }
    }

    public function deleteAvatarFile(string $path): void
    {
        $fullPath = $this->getParameter('avatars_upload') . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }


    public function updateUserPassword ($id , Request $request): jsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        $newPasswordConfirm = $data['new_password_confirm'];

        if (!$this->passwordEncoder->isPasswordValid($user, $currentPassword)) {
            return $this->json([
                'message' => 'Mot de passe actuel ne correspond pas'
            ], 400);
        }

        if (strlen($newPassword) < 8 ) {
            return $this->json([
                'message' => 'Le mot de passe doit contenir au moins 8 caractères'
            ], 400);
        }

        if ($currentPassword == $newPassword) {
            return $this->json([
                'message' => 'Le nouveau mot de passe doit être différent du mot de passe actuel'
            ], 400);
        }


        if (!$this->passwordEncoder->isPasswordValid($user, $currentPassword) || $newPassword != $newPasswordConfirm) {
            return $this->json([
                'message' => 'Mot de passe actuel incorrect'
            ], 400);
        }

        $user->setPassword($this->passwordEncoder->hashPassword($user, $newPassword));
        $user->setUpdatedAt(new DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'updated',
            'user' => $user->getUserJson()
        ]);
    }

    public function updateUserEmail ($id , Request $request): jsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return $this->json([
                'error' => 'User not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        $currentPassword = $data['current_password'];
        $newEmail = $data['new_email'];

        if (!$this->passwordEncoder->isPasswordValid($user, $currentPassword)) {
            return $this->json([
                'message' => 'Mot de passe actuel ne correspond pas'
            ], 400);
        }


        $oldUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);
        if ($oldUser) {
            return $this->json([
                'message' => 'L\'email déjà utilisé par un autre utilisateur'
            ], 400);
        }

        $user->setEmail($newEmail);
        $user->setUpdatedAt(new DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'updated',
            'user' => $user->getUserJson()
        ]);

    }


    public function getUsers(Request $request): JsonResponse
    {

        $store=  $request->get('store' , null);
        $role=  $request->get('role' , null);

        $loggedUser = $this->getUser();
        $loggedUserRole = $loggedUser->getRoles()[0];



        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u');


        if($loggedUserRole == Role::ROLE_STOREMANAGER || ($store != "" && $store != null)){
            $qb->innerJoin('u.stores', 's');
        }

        if($loggedUserRole == Role::ROLE_STOREMANAGER){
            $qb
                ->andWhere('s.id = :store')
                ->setParameter('store', $loggedUser->getStores()[0]->getId());
        }

        if ($store != "" && $store != null) {
            $qb
                ->andWhere('s.id = :store')
                ->setParameter('store', $store);
        }

        if ($role != "" && $role != null) {
            $qb->innerJoin('u.role', 'r')
                ->andWhere('r.name = :role')
                ->setParameter('role', $role);
        }

        $users = $qb->getQuery()->getResult();

        $usersJson = [];

        foreach ($users as $user) {
            $usersJson[] =
                $user->getUserJson();
        }

        return $this->json([
            'users' => $usersJson,
            'status' => 'success',
        ]);
    }



    /**
     * @throws Exception
     */
    public function saveUserProfile(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];

        $dateOfBirth =$data['dateOfBirth'];
        $dateFormat = "d/m/Y";
        $dateOfBirth = DateTime::createFromFormat($dateFormat, $dateOfBirth);

        $lastname = $data['lastname'];
        $firstname = $data['firstname'];
        $phone = $data['phone'];
        $gender = $data['gender'];



        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if(!$user){
            throw new Exception('User not found');
        }

        $user->setEmail($email);
        $user->setDateOfBirth($dateOfBirth);
        $user->setLastName($lastname);
        $user->setFirstName($firstname);
        $user->setPhone($phone);
        $user->setGender($gender);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'updated',
            'user' => $user->getUserJson()
        ]);

    }

}
