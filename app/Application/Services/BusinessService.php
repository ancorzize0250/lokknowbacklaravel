<?php

namespace App\Application\Services;

use App\Domain\Entities\Business;
use App\Domain\Repositories\BusinessRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class BusinessService
{
    /**
     * @var BusinessRepositoryInterface
     */
    protected BusinessRepositoryInterface $businessRepository;

    /**
     * BusinessService constructor.
     *
     * @param BusinessRepositoryInterface $businessRepository
     */
    public function __construct(BusinessRepositoryInterface $businessRepository)
    {
        $this->businessRepository = $businessRepository;
    }

    /**
     * Register a new business.
     *
     * @param array<string, mixed> $data
     * @return Business
     * @throws ValidationException
     */
    public function registerBusiness(array $data): Business
    {
        $validator = Validator::make($data, [
            'nit' => ['required', 'string', 'unique:business,nit'],
            'business_name' => ['required', 'string', 'max:255'],
            'owner_identification' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:business,email'],
            'phone' => ['required', 'string', 'max:20'],
            'business_address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->businessRepository->create($data);
    }
}