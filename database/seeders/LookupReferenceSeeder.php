<?php

namespace Database\Seeders;

use App\Support\GarciaContent\PositioningContentRefresher;
use Illuminate\Database\Seeder;

class LookupReferenceSeeder extends Seeder
{
    public function run(PositioningContentRefresher $refresher): void
    {
        $refresher->seed();
    }
}
