<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\Ifthenpay\Repositories;

use WHMCS\Database\Capsule;
use WHMCS\Module\Gateway\Ifthenpay\Contracts\Repositories\BaseRepositoryInterface;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

class BaseRepository implements BaseRepositoryInterface
{
    protected $tableName;
    
    public function all(): array
    {
        return [];
    }
    public function findById(string $id): array
    {
        return $this->convertObjectToArray(Capsule::table($this->table)->find($id));
    }
    public function create(array $data): void
    {
        Capsule::table($this->table)->insert([$data]);
    }

    public function createOrUpdate(array $conditions, array $data): void
    {
        Capsule::table($this->table)->updateOrInsert($conditions, $data);
    }

    public function update(array $data, string $id): void
    {
        Capsule::table($this->table)->where('id', $id)->update($data);
    }

    public function delete(string $id): void
    {
        Capsule::table($this->table)->destroy($id);
    }

    public function deleteWhere(array $conditions): void
    {
        Capsule::table($this->table)->where($conditions)->delete();
    }

    public function convertObjectToarray(object $object = null): array
    {
        return !is_null($object) ? json_decode(
            json_encode($object), true) : [];
    }
}