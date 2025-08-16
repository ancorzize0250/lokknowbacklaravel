<?php

namespace App\Http\Controllers;

use App\Application\Services\AuthService;
use App\Application\Services\ClientService;
use App\Application\Services\BusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @var ClientService
     */
    protected ClientService $clientService;

    /**
     * @var BusinessService
     */
    protected BusinessService $businessService;

    /**
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * AuthController constructor.
     *
     * @param ClientService $clientService
     * @param BusinessService $businessService
     * @param AuthService $authService
     */
    public function __construct(
        ClientService $clientService,
        BusinessService $businessService,
        AuthService $authService
    ) {
        $this->clientService = $clientService;
        $this->businessService = $businessService;
        $this->authService = $authService;
    }

    /**
     * Register a new client.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerClient(Request $request): JsonResponse
    {
        try {
            $client = $this->clientService->registerClient($request->all());
            return response()->json([
                'message' => 'Client registered successfully',
                'client' => $client,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during client registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register a new business.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerBusiness(Request $request): JsonResponse
    {
        try {
            $business = $this->businessService->registerBusiness($request->all());
            return response()->json([
                'message' => 'Business registered successfully',
                'business' => $business,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during business registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticate a user (client or business).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $userType = $request->input('userType');
            $email = $request->input('email');
            $password = $request->input('password');
            $authenticatedUser = $this->authService->login($userType, $email, $password);

            if ($authenticatedUser) {
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $authenticatedUser,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}