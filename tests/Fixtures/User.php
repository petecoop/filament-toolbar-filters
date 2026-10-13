<?php

declare(strict_types=1);

namespace Petecoop\FilamentToolbarFilters\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $guarded = [];
}
