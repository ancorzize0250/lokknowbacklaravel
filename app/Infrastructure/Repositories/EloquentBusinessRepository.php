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
     * Update a business by ID.
     *
     * @param array<string, mixed> $data
     * @return Business|null
     */
    public function update(array $data): ?Business
    {
        $id = $data['id'];
        $business = Business::find($id);

        if (! $business) {
            return null;
        }

        $business->update($data);

        return $business;
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