<?php

namespace App\Models;

class TravelRequest extends GaModel
{
    protected $casts = ['departure_date' => 'date', 'return_date' => 'date', 'estimated_cost' => 'decimal:2'];
}
