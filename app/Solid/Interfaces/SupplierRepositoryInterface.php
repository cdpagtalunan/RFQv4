<?php
namespace App\Solid\Interfaces;

interface SupplierRepositoryInterface
{
    public function getSupplierWithCondition(array $condition);
    // public function insert(array $data);
}