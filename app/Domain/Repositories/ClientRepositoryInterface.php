<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Client;

interface ClientRepositoryInterface
{
    /**
     * Create a new client.
     *
     * @param array<string, mixed> $data
     * @return Client
     */
    public function create(array $data): Client;

    /**
     * Find a client by email.
     *
     * @param string $email
     * @return Client|null
     */
    public function findByEmail(string $email): ?Client;
}