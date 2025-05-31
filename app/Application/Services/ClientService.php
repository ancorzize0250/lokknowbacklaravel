<?php

namespace App\Application\Services;

use App\Domain\Entities\Client;
use App\Domain\Repositories\ClientRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ClientService
{
    /**
     * @var ClientRepositoryInterface
     */
    protected ClientRepositoryInterface $clientRepository;

    /**
     * ClientService constructor.
     *
     * @param ClientRepositoryInterface $clientRepository
     */
    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Register a new client.
     *
     * @param array<string, mixed> $data
     * @return Client
     * @throws ValidationException
     */
    public function registerClient(array $data): Client
    {
        $validator = Validator::make($data, [
            'identification' => ['required', 'string', 'unique:client,identification'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:client,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->clientRepository->create($data);
    }
}