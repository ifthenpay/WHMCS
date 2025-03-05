<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

interface BaseRepositoryInterface 
{
    public function findById(string $id): array;
    public function create(array $data): void;
    public function createOrUpdate(array $conditions, array $data): void;
    public function update(array $data, string $id): void;
    public function delete(string $id): void;
    public function deleteWhere(array $conditions): void;
    public function convertObjectToarray(object $object = null): array;
}
