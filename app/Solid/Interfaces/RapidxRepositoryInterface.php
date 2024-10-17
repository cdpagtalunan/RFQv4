<?php
namespace App\Solid\Interfaces;

interface RapidxRepositoryInterface
{
    public function getRapidxUserWithCondition(array $condition);
}