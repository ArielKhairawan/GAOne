<?php

namespace App\Models;

class TravelRealization extends GaModel
{
    protected $casts = ['realized_at' => 'date'];
}
