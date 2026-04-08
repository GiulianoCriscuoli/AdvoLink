<?php

namespace App\Repositories;
use App\Models\Tenant;

class TenantRepository extends RepositoryBase
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }
}
