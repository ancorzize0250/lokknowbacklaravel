<?php

namespace App\Application\Services;

use App\Domain\Entities\Business;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Domain\Repositories\BusinessRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\DTO\UserDTO;

class AuthService
{
    /**
     * @var ClientRepositoryInterface
     */
    protected ClientRepositoryInterface $clientRepository;

    /**
     * @var BusinessRepositoryInterface
     */
    protected BusinessRepositoryInterface $businessRepository;

    /**
     * AuthService constructor.
     *
     * @param ClientRepositoryInterface $clientRepository
     * @param BusinessRepositoryInterface $businessRepository
     */
    public function __construct(
        ClientRepositoryInterface $clientRepository,
        BusinessRepositoryInterface $businessRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->businessRepository = $businessRepository;
    }

    /**
     * Authenticate a user.
     *
     * @param string $userType
     * @param string $email
     * @param string $password
     * @return array<string, mixed>|null
     * @throws ValidationException
     */
    public function login(string $userType, string $email, string $password): ?array
    {
        $validator = Validator::make(
            ['userType' => $userType, 'email' => $email, 'password' => $password],
            [
                'userType' => ['required', 'in:client,business'],
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($userType === 'client') {
            $user = $this->clientRepository->findByEmail($email);
            if ($user && Hash::check($password, $user->password)) {
                $userArray = $user->toArray();
                
                $userDto = new UserDTO;
                $userDto->setId($userArray['id']);
                $userDto->setIdentification($userArray['identification']);
                $userDto->setName($userArray['name']);
                $userDto->setEmail($userArray['email']);
                $userDto->setPhone($userArray['phone']);
                return [
                    'userType' => 'client',
                    'user' => $userDto->to_array(),
                ];
            }
        } elseif ($userType === 'business') {
            $user = $this->businessRepository->findByEmail($email);
            if ($user && Hash::check($password, $user->password)) {
                $userArray = $user->toArray();

                $userDto = new UserDTO;
                $userDto->setId($userArray['id']);
                $userDto->setIdentification($userArray['nit']);
                $userDto->setName($userArray['business_name']);
                $userDto->setEmail($userArray['email']);
                $userDto->setPhone($userArray['phone']);
                return [
                    'userType' => 'business',
                    'user' => $userDto->to_array(),
                ];
            }
        }

        return null; // Login failed
    }
}