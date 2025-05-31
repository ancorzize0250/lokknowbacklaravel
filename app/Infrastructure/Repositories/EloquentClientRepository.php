<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Client;
use App\Domain\Repositories\ClientRepositoryInterface;

class EloquentClientRepository implements ClientRepositoryInterface
{
    /**
     * Create a new client.
     *
     * @param array<string, mixed> $data
     * @return Client
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Find a client by email.
     *
     * @param string $email
     * @return Client|null
     */
    public function findByEmail(string $email): ?Client
    {
        return Client::where('email', $email)->first();
    }
}