<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emotion;
use App\Repositories\EmotionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmotionController extends Controller
{
    protected $emotionRepository;

    public function __construct(EmotionRepository $emotionRepository)
    {
        $this->emotionRepository = $emotionRepository;
    }

    public function index()
    {
        $emotions = $this->emotionRepository->getBaseEmotions();
        return view('emotions.index', compact('emotions'));
    }

    public function create()
    {
        $emotionsBase = $this->emotionRepository->getBaseEmotions();
        return view('emotions.create', compact('emotionsBase'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:emotions,id',
            'image_icone' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_icone')) {
            $data['image_icone'] = $request->file('image_icone')->store('emotions', 'public');
        }

        $this->emotionRepository->create($data);

        return redirect()->route('emotions.index')->with('success', 'Émotion créée !');
    }

    public function edit(Emotion $emotion)
    {
        // On récupère les parents potentiels (excluant l'émotion elle-même)
        $emotionsBase = Emotion::whereNull('parent_id')
            ->where('id', '!=', $emotion->id)
            ->get();

        return view('emotions.edit', compact('emotion', 'emotionsBase'));
    }

    public function update(Request $request, Emotion $emotion)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:emotions,id',
            'image_icone' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_icone')) {
            // Suppression de l'ancien fichier s'il existe
            if ($emotion->image_icone) {
                Storage::disk('public')->delete($emotion->image_icone);
            }
            $data['image_icone'] = $request->file('image_icone')->store('emotions', 'public');
        }

        $this->emotionRepository->update($emotion, $data);

        return redirect()->route('emotions.index')->with('success', 'Émotion mise à jour !');
    }

    public function destroy(Emotion $emotion)
    {
        // On délègue toute la suppression (enfants + images) au repository
        $this->emotionRepository->deleteWithChildren($emotion);

        return response()->json(['success' => true]);
    }

    public function showSubEmotions(Emotion $emotion)
    {
        $emotions = $this->emotionRepository->getChildren($emotion);
        $parentNom = $emotion->nom;

        return view('emotions.index', compact('emotions', 'parentNom'));
    }
}
