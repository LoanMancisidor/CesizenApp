<?php

namespace App\Repositories;

use App\Models\UserEmotion;
use Illuminate\Support\Facades\DB;

class UserEmotionRepository
{
    public function getUserHistory($userId)
    {
        return UserEmotion::with('emotion')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function store($userId, $emotionId)
    {
        $userEmotion = UserEmotion::create([
            'user_id' => $userId,
            'emotion_id' => $emotionId,
        ]);

        return $userEmotion->load('emotion');
    }

    public function delete($userId, $id)
    {
        return UserEmotion::where('user_id', $userId)
            ->where('id', $id)
            ->delete();
    }

    public function getStats($userId)
    {
        return UserEmotion::where('user_id', $userId)
            ->join('emotions', 'user_emotions.emotion_id', '=', 'emotions.id')
            ->selectRaw('emotions.nom, count(*) as total')
            ->groupBy('emotions.nom')
            ->get();
    }
}