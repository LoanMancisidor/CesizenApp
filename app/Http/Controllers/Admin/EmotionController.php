<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmotionController extends Controller
{
    public function index()
    {
        $emotions = Emotion::whereNull('parent_id')->get();
        return view('emotions.index', compact('emotions'));
    }

    public function create() {
        $emotionsBase = Emotion::whereNull('parent_id')->get();
        return view('emotions.create', compact('emotionsBase'));
    }

    public function store(Request $request) {
        $request->validate([
            'nom' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:emotions,id', // On vérifie que le parent existe bien
            'image_icone' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        // Si un parent_id est présent, on force le niveau à 2
        if (!empty($request->parent_id)) {
            $data['niveau'] = 2;
        } else {
            $data['niveau'] = 1;
            $data['parent_id'] = null;
        }

        if ($request->hasFile('image_icone')) {
            $data['image_icone'] = $request->file('image_icone')->store('emotions', 'public');
        }

        Emotion::create($data);
        return redirect()->route('emotions.index')->with('success', 'Émotion créée !');
    }

    public function edit(Emotion $emotion)
    {
        $emotionsBase = Emotion::whereNull('parent_id')
                                ->where('id', '!=', $emotion->id) // On évite qu'une émotion soit son propre parent
                                ->get();

        return view('emotions.edit', compact('emotion', 'emotionsBase'));
    }

    public function update(Request $request, Emotion $emotion) {
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'nullable|in:1,2',
            'parent_id' => 'nullable|exists:emotions,id',
            'image_icone' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        // Logique de niveau automatique selon le parent
        $data['niveau'] = !empty($request->parent_id) ? 2 : 1;
        if (empty($request->parent_id)) $data['parent_id'] = null;

        if ($request->hasFile('image_icone')) {
            if ($emotion->image_icone) {
                Storage::disk('public')->delete($emotion->image_icone);
            }
            $data['image_icone'] = $request->file('image_icone')->store('emotions', 'public');
        }

        $emotion->update($data);
        return redirect()->route('emotions.index')->with('success', 'Émotion mise à jour !');
    }

    public function destroy(Emotion $emotion) {
        // 1. Supprimer les émotions secondaires (enfants)
        foreach ($emotion->enfants as $enfant) {
            // Supprimer l'icône de l'enfant s'il en a une
            if ($enfant->image_icone) {
                Storage::disk('public')->delete($enfant->image_icone);
            }
            $enfant->delete();
        }

        // 2. Supprimer l'icône du parent
        if ($emotion->image_icone) {
            Storage::disk('public')->delete($emotion->image_icone);
        }

        // 3. Supprimer le parent
        $emotion->delete();

        return response()->json(['success' => true]);
    }

    public function showSubEmotions(Emotion $emotion)
    {
        $emotions = Emotion::where('parent_id', $emotion->id)->get();
        $parentNom = $emotion->nom;

        return view('emotions.index', compact('emotions', 'parentNom'));
    }
}