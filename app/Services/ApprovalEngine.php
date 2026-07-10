<?php

namespace App\Services;

use App\Models\ApprovalAction;
use App\Models\ApprovalInstance;
use App\Models\ApprovalWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalEngine
{
    public function submit(Model $record, string $module): ApprovalInstance
    {
        return DB::transaction(function () use ($record, $module) {
            $workflow = ApprovalWorkflow::query()
                ->where('module', $module)
                ->where('is_active', true)
                ->with('steps')
                ->firstOrFail();

            $record->forceFill(['status' => 'submitted'])->save();

            return ApprovalInstance::create([
                'approval_workflow_id' => $workflow->id,
                'approvable_type' => $record::class,
                'approvable_id' => $record->getKey(),
                'requested_by' => auth()->id(),
                'status' => 'pending',
                'current_sequence' => $workflow->steps->first()?->sequence ?? 1,
                'submitted_at' => now(),
            ]);
        });
    }

    public function act(ApprovalInstance $instance, string $action, ?string $notes = null): void
    {
        DB::transaction(function () use ($instance, $action, $notes) {
            $workflow = $instance->approvalWorkflow ?? ApprovalWorkflow::with('steps')->findOrFail($instance->approval_workflow_id);
            $step = $workflow->steps->firstWhere('sequence', $instance->current_sequence);

            ApprovalAction::create([
                'approval_instance_id' => $instance->id,
                'approval_step_id' => $step?->id,
                'actor_id' => auth()->id(),
                'action' => $action,
                'notes' => $notes,
                'acted_at' => now(),
            ]);

            if ($action === 'reject') {
                $instance->update(['status' => 'rejected', 'completed_at' => now()]);
                $instance->approvable?->forceFill(['status' => 'rejected'])->save();
                return;
            }

            if ($action === 'revision') {
                $instance->update(['status' => 'revision']);
                $instance->approvable?->forceFill(['status' => 'revision'])->save();
                return;
            }

            $next = $workflow->steps->where('sequence', '>', $instance->current_sequence)->first();
            if ($next) {
                $instance->update(['current_sequence' => $next->sequence]);
                return;
            }

            $instance->update(['status' => 'approved', 'completed_at' => now()]);
            $instance->approvable?->forceFill(['status' => 'approved'])->save();
        });
    }
}
