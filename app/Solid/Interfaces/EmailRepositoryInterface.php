<?php
namespace App\Solid\Interfaces;

interface EmailRepositoryInterface
{
    public function sendEmail(array $emailArray);
    public function sendEmailFollowup(array $emailArray);
}