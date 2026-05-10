<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserEmotionRepository; // <--- Importation
use Illuminate\Http\Request;

class UserEmotionController extends Controller
{
    protected $repository;

    public function __construct(UserEmotionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $history = $this->repository->getUserHistory($request->user()->id);
        return response()->json($history);
    }

    public function store(Request $request)
    {
        $request->validate([
            'emotion_id' => 'required|exists:emotions,id',
        ]);

        $userEmotion = $this->repository->store($request->user()->id, $request->emotion_id);
        return response()->json($userEmotion, 201);
    }

    public function destroy(Request $request, $id)
    {
        $deleted = $this->repository->delete($request->user()->id, $id);

        if (!$deleted) {
            return response()->json(['message' => 'Entrée non trouvée'], 404);
        }

        return response()->json(['message' => 'Entrée supprimée']);
    }

    public function stats(Request $request)
    {
        $stats = $this->repository->getStats($request->user()->id);
        return response()->json($stats);
    }
}