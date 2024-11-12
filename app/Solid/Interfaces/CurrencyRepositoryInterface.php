<?php
namespace App\Solid\Interfaces;

interface CurrencyRepositoryInterface
{
    public function insert(array $data);
    public function getAllCurrency();
    public function update(int $id, array $data);
}