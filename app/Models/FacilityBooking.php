<?php

namespace App\Models;

class FacilityBooking extends GaModel
{
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
}
