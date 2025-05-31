<?php

namespace Tests\Unit;

use App\Application\Services\BusinessService;
use App\Domain\Entities\Business;
use App\Domain\Repositories\BusinessRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use App\Infrastructure\Repositories\EloquentBusinessRepository;
use Tests\TestCase;
use Mockery;

class BusinessRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var BusinessRepositoryInterface|\Mockery\MockInterface
     */
    protected $businessRepositoryMock;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->businessRepositoryMock = Mockery::mock(BusinessRepositoryInterface::class);
        $this->app->instance(BusinessRepositoryInterface::class, $this->businessRepositoryMock);
    }

    /**
     * Clean up the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful business registration.
     *
     * @return void
     */
    public function testSuccessfulBusinessRegistration(): void
    {
        $negocioData = [
            'nitOrIdentificacion' => 'NIT123456789',
            'businessName' => 'My Awesome Business',
            'ownerIdentification' => 'OWNERID123',
            'ownerName' => 'Jane Doe',
            'email' => 'business@example.com',
            'celular' => '0987654321',
            'businessAddress' => '123 Business St',
            'password' => 'businesspass123',
        ];

        $negocio = new Business($negocioData);
        $negocio->id = 1; // Simulate an ID for the created business

        $this->businessRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($negocioData) {
                // Ensure password is not hashed yet for this check, it's handled by model mutator
                return $arg['nitOrIdentificacion'] === $negocioData['nitOrIdentificacion'] &&
                       $arg['email'] === $negocioData['email'];
            }))
            ->andReturn($negocio);

        $service = new BusinessService($this->businessRepositoryMock);
        $result = $service->registerBusiness($negocioData);

        $this->assertInstanceOf(Business::class, $result);
        $this->assertEquals('business@example.com', $result->email);
        $this->assertEquals('My Awesome Business', $result->nombre_negocio);
    }

    /**
     * Test business registration with invalid email.
     *
     * @return void
     */
    public function testBusinessRegistrationWithInvalidEmail(): void
    {
        $negocioData = [
            'nitOrIdentificacion' => 'NIT123456789',
            'businessName' => 'My Awesome Business',
            'ownerIdentification' => 'OWNERID123',
            'ownerName' => 'Jane Doe',
            'email' => 'invalid-business-email', // Invalid email
            'celular' => '0987654321',
            'businessAddress' => '123 Business St',
            'password' => 'businesspass123',
        ];

        $this->expectException(ValidationException::class);
        $this->businessRepositoryMock->shouldNotReceive('create');

        $service = new BusinessService($this->businessRepositoryMock);
        $service->registerBusiness($negocioData);
    }

    /**
     * Test business registration with missing required field (businessName).
     *
     * @return void
     */
    public function testBusinessRegistrationWithMissingRequiredField(): void
    {
        $negocioData = [
            'nitOrIdentificacion' => 'NIT123456789',
            // 'businessName' => 'My Awesome Business', // Missing
            'ownerIdentification' => 'OWNERID123',
            'ownerName' => 'Jane Doe',
            'email' => 'business@example.com',
            'celular' => '0987654321',
            'businessAddress' => '123 Business St',
            'password' => 'businesspass123',
        ];

        $this->expectException(ValidationException::class);
        $this->businessRepositoryMock->shouldNotReceive('create');

        $service = new BusinessService($this->businessRepositoryMock);
        $service->registerBusiness($negocioData);
    }

    /**
     * Test business registration with duplicate email.
     *
     * @return void
     */
    public function testBusinessRegistrationWithDuplicateEmail(): void
    {
        // First, create a business directly to simulate an existing one
        Business::create([
            'nit_o_identificacion' => 'NIT000000000',
            'nombre_negocio' => 'Existing Business',
            'identificacion_propietario' => 'OWNER000',
            'nombre_propietario' => 'Existing Owner',
            'email' => 'duplicate_biz@example.com',
            'celular' => '0000000000',
            'direccion_negocio' => 'Existing Address',
            'password' => 'existingbizpass',
        ]);

        $negocioData = [
            'nitOrIdentificacion' => 'NIT111111111',
            'businessName' => 'New Business',
            'ownerIdentification' => 'OWNER111',
            'ownerName' => 'New Owner',
            'email' => 'duplicate_biz@example.com', // Duplicate email
            'celular' => '1111111111',
            'businessAddress' => 'New Address',
            'password' => 'newbizpass',
        ];

        $this->expectException(ValidationException::class);
        $this->businessRepositoryMock->shouldNotReceive('create'); // Should not reach the repository

        // Use the actual repository for validation to catch unique constraints
        $service = new BusinessService(new EloquentBusinessRepository());
        $service->registerBusiness($negocioData);
    }
}