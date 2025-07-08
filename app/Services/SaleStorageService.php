<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaleStorageService
{
    private string $filePath = 'sales.json';

    public function all(): array
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }
        return json_decode(Storage::get($this->filePath), true);
    }

    public function create(array $data): array
    {
        $sales = $this->all();

        $newSale = array_merge($data, [
            'id' => (string) Str::uuid() // Gera um ID único para cada simulação
        ]);

        $sales[] = $newSale;

        Storage::put($this->filePath, json_encode($sales, JSON_PRETTY_PRINT));

        return $newSale;
    }
    
    public function delete(string $id): bool
    {
        $sales = $this->all();
        $initialCount = count($sales);

        $filteredSales = array_filter($sales, fn($sale) => $sale['id'] !== $id);

        if (count($filteredSales) < $initialCount) {
            Storage::put($this->filePath, json_encode(array_values($filteredSales), JSON_PRETTY_PRINT));
            return true;
        }

        return false;
    }
}