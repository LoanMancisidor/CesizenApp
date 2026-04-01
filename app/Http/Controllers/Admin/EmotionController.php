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

    public function create()
    {
        $emotionsBase = Emotion::whereNull('parent_id')->get();
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

    public function update(Request $request, Emotion $emotion)
    {
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

    public function destroy(Emotion $emotion)
    {
        // Supprimer les émotions secondaires (enfants)
        foreach ($emotion->enfants as $enfant) {
            if ($enfant->image_icone) {
                Storage::disk('public')->delete($enfant->image_icone);
            }
            $enfant->delete();
        }

        if ($emotion->image_icone) {
            Storage::disk('public')->delete($emotion->image_icone);
        }

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
