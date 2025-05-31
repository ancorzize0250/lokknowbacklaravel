<?php

namespace Tests\Unit;

use App\Application\Services\AuthService;
use App\Domain\Entities\Client;
use App\Domain\Entities\Business;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Domain\Repositories\BusinessRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Mockery;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ClientRepositoryInterface|\Mockery\MockInterface
     */
    protected $clientRepositoryMock;

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
        $this->clientRepositoryMock = Mockery::mock(ClientRepositoryInterface::class);
        $this->businessRepositoryMock = Mockery::mock(BusinessRepositoryInterface::class);
        $this->app->instance(ClientRepositoryInterface::class, $this->clientRepositoryMock);
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
     * Test successful client login.
     *
     * @return void
     */
    public function testSuccessfulClientLogin(): void
    {
        $password = 'password123';
        $hashedPassword = Hash::make($password);

        $client = new Client([
            'identificacion' => '123456789',
            'nombre' => 'Test Client',
            'email' => 'client@example.com',
            'celular' => '1234567890',
            'password' => $hashedPassword,
        ]);

        $this->clientRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with('client@example.com')
            ->andReturn($client);

        $this->businessRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $result = $authService->login('client', 'client@example.com', $password);

        $this->assertNotNull($result);
        $this->assertEquals('client', $result['userType']);
        $this->assertEquals('client@example.com', $result['user']['email']);
    }

    /**
     * Test successful business login.
     *
     * @return void
     */
    public function testSuccessfulBusinessLogin(): void
    {
        $password = 'businesspass';
        $hashedPassword = Hash::make($password);

        $business = new Business([
            'nit_o_identificacion' => 'NIT123',
            'nombre_negocio' => 'Test Business',
            'identificacion_propietario' => 'OWNERID',
            'nombre_propietario' => 'Test Owner',
            'email' => 'business@example.com',
            'celular' => '0987654321',
            'direccion_negocio' => 'Business Address',
            'password' => $hashedPassword,
        ]);

        $this->businessRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with('business@example.com')
            ->andReturn($business);

        $this->clientRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $result = $authService->login('business', 'business@example.com', $password);

        $this->assertNotNull($result);
        $this->assertEquals('business', $result['userType']);
        $this->assertEquals('business@example.com', $result['user']['email']);
    }

    /**
     * Test client login with invalid password.
     *
     * @return void
     */
    public function testClientLoginWithInvalidPassword(): void
    {
        $password = 'password123';
        $hashedPassword = Hash::make($password);

        $client = new Client([
            'identificacion' => '123456789',
            'nombre' => 'Test Client',
            'email' => 'client@example.com',
            'celular' => '1234567890',
            'password' => $hashedPassword,
        ]);

        $this->clientRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with('client@example.com')
            ->andReturn($client);

        $this->businessRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $result = $authService->login('client', 'client@example.com', 'wrongpassword');

        $this->assertNull($result);
    }

    /**
     * Test login with non-existent email.
     *
     * @return void
     */
    public function testLoginWithNonExistentEmail(): void
    {
        $this->clientRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        $this->businessRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $result = $authService->login('client', 'nonexistent@example.com', 'somepassword');

        $this->assertNull($result);
    }

    /**
     * Test login with invalid user type.
     *
     * @return void
     */
    public function testLoginWithInvalidUserType(): void
    {
        $this->expectException(ValidationException::class);
        $this->clientRepositoryMock->shouldNotReceive('findByEmail');
        $this->businessRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $authService->login('invalidType', 'test@example.com', 'password');
    }

    /**
     * Test login with invalid email format.
     *
     * @return void
     */
    public function testLoginWithInvalidEmailFormat(): void
    {
        $this->expectException(ValidationException::class);
        $this->clientRepositoryMock->shouldNotReceive('findByEmail');
        $this->businessRepositoryMock->shouldNotReceive('findByEmail');

        $authService = new AuthService($this->clientRepositoryMock, $this->businessRepositoryMock);
        $authService->login('client', 'invalid-email', 'password');
    }
}