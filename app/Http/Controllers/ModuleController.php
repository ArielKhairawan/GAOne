<?php

namespace App\Http\Controllers;

use App\Services\ApprovalEngine;
use App\Support\Ga1Modules;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(string $module)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'view');
        $records = $meta['model']::query()->latest()->paginate(15);

        return view('modules.index', compact('meta', 'records'));
    }

    public function create(string $module)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'create');
        $record = new $meta['model'];

        return view('modules.form', compact('meta', 'record'));
    }

    public function store(Request $request, string $module)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'create');
        $data = $this->validated($request, $meta);
        $data = array_merge($meta['defaults'] ?? [], $data);
        if ($meta['owned'] ?? false) {
            $data['user_id'] = $request->user()->id;
        }
        if ($meta['creator'] ?? false) {
            $data['created_by'] = $request->user()->id;
        }
        $meta['model']::create($data);

        return redirect()->route('modules.index', $module)->with('status', 'Data dibuat.');
    }

    public function edit(string $module, int $id)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'edit');
        $record = $meta['model']::findOrFail($id);

        return view('modules.form', compact('meta', 'record'));
    }

    public function update(Request $request, string $module, int $id)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'edit');
        $record = $meta['model']::findOrFail($id);
        $record->update($this->validated($request, $meta, $id));

        return redirect()->route('modules.index', $module)->with('status', 'Data diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'delete');
        $meta['model']::findOrFail($id)->delete();

        return back()->with('status', 'Data dihapus.');
    }

    public function submit(string $module, int $id, ApprovalEngine $engine)
    {
        $meta = Ga1Modules::get($module);
        $this->authorizeModule($meta, 'create');
        $engine->submit($meta['model']::findOrFail($id), $meta['permission']);

        return back()->with('status', 'Diajukan ke approval.');
    }

    private function validated(Request $request, array $meta, ?int $id = null): array
    {
        $rules = collect($meta['fields'])->map(fn ($rule) => str_replace('{id}', (string) ($id ?? 'NULL'), $rule))->all();

        return $request->validate($rules);
    }

    private function authorizeModule(array $meta, string $ability): void
    {
        abort_unless(auth()->user()->can($meta['permission'].'.'.$ability), 403);
    }
}
