<?php
namespace App\Solid\Interfaces;

interface RequestRepositoryInterface
{
    // Request
    public function insertGetId(array $data);
    public function update(int $id, array $data);
    public function getQuotationRequestWithConditionAndRelation(array $conditions, array $relations);
    
    // Item
    public function insertItem(array $data);
    // public function getRequestItemWithCondition(array $condition);
    public function getRequestItemWithConditionAndRelation(array $conditions, array $relations);
    public function deleteItem(int $id);

    // Item Quotation
    public function insertItemQuotation(array $data);
    public function updateItemQuotation(int $id, array $data);
    public function updateItemQuotationWithConditionAndRelation(array $condition, array $data, array $relation);
    public function getSupplierQuotationWithCondition(array $condition);
    public function deleteQuotation(int $id);
}