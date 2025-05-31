<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Business;
use App\Domain\Repositories\BusinessRepositoryInterface;

class EloquentBusinessRepository implements BusinessRepositoryInterface
{
    /**
     * Create a new business.
     *
     * @param array<string, mixed> $data
     * @return Business
     */
    public function create(array $data): Business
    {
        return Business::create($data);
    }

    /**
     * Find a business by email.
     *
     * @param string $email
     * @return Business|null
     */
    public function findByEmail(string $email): ?Business
    {
        return Business::where('email', $email)->first();
    }
}