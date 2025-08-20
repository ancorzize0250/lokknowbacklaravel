<?php

namespace App\Http\Controllers;

use App\Application\Services\BusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BusinessController extends Controller
{
    /**
     * @var BusinessService
     */
    protected BusinessService $businessService;

    /**
     * BusinessController constructor.
     *
     * @param BusinessService $businessService
     */
    public function __construct(
        BusinessService $businessService
    ) {
        $this->businessService = $businessService;
    }

    /**
     * Edit business information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editBusiness(Request $request): JsonResponse
    {
        try {
            $business = $this->businessService->editBusiness($request->all());
            return response()->json([
                'message' => 'Información del negocio registrada correctamente',
                'business' => $business,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ha ocurrido un error durante la edición de la información',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
}