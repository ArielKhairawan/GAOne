<?php

namespace App\Models;

class SystemNotification extends GaModel
{
    protected $casts = ['read_at' => 'datetime', 'sent_at' => 'datetime'];
}
