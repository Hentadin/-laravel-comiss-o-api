<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Services\CommissionService;
use App\Services\SaleStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SaleController extends Controller
{
    public function __construct(
        private CommissionService $commissionService,
        private SaleStorageService $saleStorageService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->saleStorageService->all());
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $totalValue = (float) $validatedData['valor_total'];
        $saleType = $validatedData['tipo_venda'];
        $commissions = $this->commissionService->calculate($totalValue, $saleType);

        $simulationData = [
            'valor_total' => $totalValue,
            'tipo_venda' => $saleType,
            'comissoes' => $commissions
        ];


        $newSale = $this->saleStorageService->create($simulationData);

        return response()->json($newSale, Response::HTTP_CREATED);
    }

    public function destroy(string $id): Response
    {
        $deleted = $this->saleStorageService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Simulação não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return response()->noContent();
    }
}