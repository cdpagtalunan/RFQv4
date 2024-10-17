<?php
namespace App\Solid\Interfaces;

interface UserAccessRepositoryInterface
{
    public function getUAccessWithCondition(array $condition);
    public function getAllUserAccess();
    public function getUserWithRelationAndCondition(array $condition, array $relation);
    public function insert(array $data);
    public function update(int $id, array $data);
}