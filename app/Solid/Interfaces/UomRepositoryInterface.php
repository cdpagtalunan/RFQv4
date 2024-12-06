<?php
namespace App\Solid\Interfaces;

interface UomRepositoryInterface
{
    public function getUomWithCondition(array $condition);
    public function insert(array $data);
    public function update(int $id, array $data);
}