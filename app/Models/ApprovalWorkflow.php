<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalWorkflow extends GaModel
{
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('sequence');
    }
}
