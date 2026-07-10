<?php

namespace App\Models;

class PurchaseOrder extends GaModel
{
    protected $casts = ['po_date' => 'date', 'total_amount' => 'decimal:2'];
}
