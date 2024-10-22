<?php
namespace App\Solid\Interfaces;

interface CategoryRepositoryInterface
{
    public function getAllCategory();
    public function insert(array $data);
    public function update(int $id,array $data);
}