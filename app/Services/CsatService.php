<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CsatService
{
    public function __construct(private SatisfactionNotificationService $notifier)
    {
    }

    /**
     * Buat survei "pending" untuk sebuah record yang baru saja selesai
     * (Booking Meeting, Permintaan Konsumsi, Permintaan ATK, Pengaduan).
     * Dipanggil oleh service modul terkait saat status berubah ke "selesai".
     */
    public function requestFeedback(Model $record, string $serviceType, int $userId): Survey
    {
        return Survey::create([
            'surveyable_type' => $record::class,
            'surveyable_id' => $record->getKey(),
            'user_id' => $userId,
            'service_type' => $serviceType,
            'status' => 'pending',
            'sent_at' => now(),
        ]);
    }

    public function pendingFor(int $userId): Collection
    {
        return Survey::query()->where('user_id', $userId)->pending()->latest()->get();
    }

    public function submit(Survey $survey, int $rating, ?string $comments): SurveyResponse
    {
        $response = SurveyResponse::create([
            'survey_id' => $survey->id,
            'speed_score' => $rating,
            'service_score' => $rating,
            'satisfaction_score' => $rating,
            'comments' => $comments,
        ]);

        $survey->update(['status' => 'completed', 'completed_at' => now()]);

        $this->notifier->checkLowRating($response);

        return $response;
    }
}
