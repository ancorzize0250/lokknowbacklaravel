<?php

namespace Tests\Unit;

use App\Application\Services\ClientService;
use App\Domain\Entities\Client;
use App\Domain\Repositories\ClientRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use App\Infrastructure\Repositories\EloquentClientRepository;

use Tests\TestCase;
use Mockery;

class ClienteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ClientRepositoryInterface|\Mockery\MockInterface
     */
    protected $clientRepositoryMock;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepositoryMock = Mockery::mock(ClientRepositoryInterface::class);
        $this->app->instance(ClientRepositoryInterface::class, $this->clientRepositoryMock);
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
     * Test successful client registration.
     *
     * @return void
     */
    public function testSuccessfulClientRegistration(): void
    {
        $clienteData = [
            'identificacion' => '123456789',
            'nombre' => 'John Doe',
            'email' => 'john.doe@example.com',
            'celular' => '1234567890',
            'password' => 'password123',
        ];

        $cliente = new Client($clienteData);
        $cliente->id = 1; // Simulate an ID for the created client

        $this->clientRepositoryMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($clienteData) {
                // Ensure password is not hashed yet for this check, it's handled by model mutator
                return $arg['identificacion'] === $clienteData['identificacion'] &&
                       $arg['email'] === $clienteData['email'];
            }))
            ->andReturn($cliente);

        $service = new ClientService($this->clientRepositoryMock);
        $result = $service->registerClient($clienteData);

        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals('john.doe@example.com', $result->email);
        $this->assertEquals('John Doe', $result->nombre);
    }

    /**
     * Test client registration with invalid email.
     *
     * @return void
     */
    public function testClientRegistrationWithInvalidEmail(): void
    {
        $clienteData = [
            'identificacion' => '123456789',
            'nombre' => 'John Doe',
            'email' => 'invalid-email', // Invalid email
            'celular' => '1234567890',
            'password' => 'password123',
        ];

        $this->expectException(ValidationException::class);
        $this->clientRepositoryMock->shouldNotReceive('create');

        $service = new ClientService($this->clientRepositoryMock);
        $service->registerClient($clienteData);
    }

    /**
     * Test client registration with missing required field (name).
     *
     * @return void
     */
    public function testClientRegistrationWithMissingRequiredField(): void
    {
        $clienteData = [
            'identificacion' => '123456789',
            'email' => 'john.doe@example.com',
            'celular' => '1234567890',
            'password' => 'password123',
        ]; // Missing 'nombre'

        $this->expectException(ValidationException::class);
        $this->clientRepositoryMock->shouldNotReceive('create');

        $service = new ClientService($this->clientRepositoryMock);
        $service->registerClient($clienteData);
    }

    /**
     * Test client registration with duplicate email.
     *
     * @return void
     */
    public function testClientRegistrationWithDuplicateEmail(): void
    {
        // First, create a client directly to simulate an existing one
        Client::create([
            'identificacion' => '000000000',
            'nombre' => 'Existing User',
            'email' => 'duplicate@example.com',
            'celular' => '0987654321',
            'password' => 'existingpass',
        ]);

        $clienteData = [
            'identificacion' => '111111111',
            'nombre' => 'New User',
            'email' => 'duplicate@example.com', // Duplicate email
            'celular' => '1234567890',
            'password' => 'newpassword',
        ];

        $this->expectException(ValidationException::class);
        $this->clientRepositoryMock->shouldNotReceive('create'); // Should not reach the repository

        // We need to use the actual repository for validation to catch unique constraints
        $service = new ClientService(new EloquentClientRepository());
        $service->registerClient($clienteData);
    }
}