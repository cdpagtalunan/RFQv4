<?php
namespace App\Solid\Interfaces;

interface UomRepositoryInterface
{
    public function getUomWithCondition(array $condition);
}