<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): View
    {
        $grouped = Permission::withCount('roles')->orderBy('name')->get()
            ->groupBy(fn (Permission $p) => str($p->name)->before('.')->toString());

        return view('admin.permissions.index', ['grouped' => $grouped]);
    }
}
