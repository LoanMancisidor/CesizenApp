<?php

namespace App\Repositories;

use App\Models\Emotion;
use Illuminate\Support\Facades\Storage;

class EmotionRepository
{
    public function getBaseEmotions()
    {
        return Emotion::whereNull('parent_id')->get();
    }

    public function getChildren(Emotion $parent)
    {
        return Emotion::where('parent_id', $parent->id)->get();
    }

    public function create(array $data)
    {
        $data['niveau'] = !empty($data['parent_id']) ? 2 : 1;
        return Emotion::create($data);
    }

    public function update(Emotion $emotion, array $data)
    {
        $data['niveau'] = !empty($data['parent_id']) ? 2 : 1;
        return $emotion->update($data);
    }

    public function deleteWithChildren(Emotion $emotion)
    {
        foreach ($emotion->enfants as $enfant) {
            $this->deleteSingle($enfant);
        }
        return $this->deleteSingle($emotion);
    }

    private function deleteSingle(Emotion $e)
    {
        if ($e->image_icone) Storage::disk('public')->delete($e->image_icone);
        return $e->delete();
    }
}
