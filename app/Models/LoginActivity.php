<?php

namespace App\Models;

class LoginActivity extends GaModel
{
    protected $casts = ['successful' => 'boolean', 'logged_at' => 'datetime'];
}
