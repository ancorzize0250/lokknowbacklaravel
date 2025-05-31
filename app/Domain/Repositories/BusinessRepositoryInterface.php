<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Business;

interface BusinessRepositoryInterface
{
    /**
     * Create a new business.
     *
     * @param array<string, mixed> $data
     * @return Business
     */
    public function create(array $data): Business;

    /**
     * Find a business by email.
     *
     * @param string $email
     * @return Business|null
     */
    public function findByEmail(string $email): ?Business;
}