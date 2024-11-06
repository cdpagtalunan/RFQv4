<?php
namespace App\Solid\Interfaces;

interface RequestRepositoryInterface
{
    public function insertGetId(array $data);
    public function update(int $id, array $data);
    public function getQuotationRequestWithConditionAndRelation(array $conditions, array $relations);
    
    public function insertItem(array $data);
    public function getRequestItemWithCondition(array $condition);
}