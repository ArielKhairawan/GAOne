<?php

namespace App\Services;

use App\Models\SurveyResponse;
use App\Models\SystemNotification;
use App\Models\User;

class SatisfactionNotificationService
{
    public function checkLowRating(SurveyResponse $response): void
    {
        $threshold = (int) config('monitoring.csat_low_rating_threshold');

        if ((int) $response->satisfaction_score > $threshold) {
            return;
        }

        $survey = $response->survey;
        $moduleLabel = config('monitoring.csat_modules')[$survey->service_type] ?? $survey->service_type;

        $recipients = User::role(['Admin', 'Manager'])->pluck('id');

        foreach ($recipients as $userId) {
            SystemNotification::create([
                'user_id' => $userId,
                'type' => 'csat.low_rating',
                'channel' => 'in-app',
                'title' => 'Rating Kepuasan Rendah',
                'body' => "Survei kepuasan untuk modul {$moduleLabel} mendapat rating {$response->satisfaction_score}/5 dari {$survey->user?->name}.".($response->comments ? " Komentar: \"{$response->comments}\"" : ''),
                'sent_at' => now(),
            ]);
        }
    }
}
