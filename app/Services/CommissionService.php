<?php

namespace App\Services;

use InvalidArgumentException;

class CommissionService
{
    /**
     * @param float $totalValue O valor total da venda.
     * @param string $saleType O tipo da venda ('direta' ou 'afiliada').
     * @return array As comissões calculadas.
     */
    public function calculate(float $totalValue, string $saleType): array
    {
        $platformCommission = $totalValue * 0.10;

        switch ($saleType) {
            case 'direta':
                return [
                    'plataforma' => $platformCommission,
                    'produtor' => $totalValue * 0.90,
                    'afiliado' => 0,
                ];

            case 'afiliada':
                return [
                    'plataforma' => $platformCommission,
                    'produtor' => $totalValue * 0.60,
                    'afiliado' => $totalValue * 0.30,
                ];

            default:
                throw new InvalidArgumentException("Tipo de venda '{$saleType}' é inválido.");
        }
    }
}