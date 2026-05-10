<?php

namespace Tests\Unit;

use App\Models\Emotion;
use App\Repositories\EmotionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmotionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EmotionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmotionRepository();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_get_base_emotions()
    {
        $parent = Emotion::factory()->create(['parent_id' => null]);
        $child = Emotion::factory()->create(['parent_id' => $parent->id]);

        $baseEmotions = $this->repository->getBaseEmotions();

        $this->assertCount(1, $baseEmotions);
        $this->assertTrue($baseEmotions->contains($parent));
        $this->assertFalse($baseEmotions->contains($child));
    }

    /** @test */
    public function it_can_get_children_emotions()
    {
        $parent = Emotion::factory()->create(['parent_id' => null]);
        $child1 = Emotion::factory()->create(['parent_id' => $parent->id]);
        $child2 = Emotion::factory()->create(['parent_id' => $parent->id]);
        $other = Emotion::factory()->create(['parent_id' => null]);

        $children = $this->repository->getChildren($parent);

        $this->assertCount(2, $children);
        $this->assertTrue($children->contains($child1));
        $this->assertTrue($children->contains($child2));
        $this->assertFalse($children->contains($other));
    }

    /** @test */
    public function create_base_emotion_sets_niveau_to_one()
    {
        $data = ['nom' => 'Joie', 'parent_id' => null];

        // Correction : Utilisation du bon repository ($this->repository)
        $emotion = $this->repository->create($data);

        $this->assertEquals(1, $emotion->niveau);
        $this->assertEquals('Joie', $emotion->nom);
    }

    /** @test */
    public function create_child_emotion_sets_niveau_to_two()
    {
        $parent = Emotion::factory()->create(['parent_id' => null, 'niveau' => 1]);
        $data = [
            'nom' => 'Ravi', // Correction : 'nom' au lieu de 'name'
            'parent_id' => $parent->id,
        ];

        $emotion = $this->repository->create($data);

        $this->assertEquals('Ravi', $emotion->nom);
        $this->assertEquals($parent->id, $emotion->parent_id);
        $this->assertEquals(2, $emotion->niveau);
    }

    /** @test */
    public function update_base_emotion_sets_niveau_to_one()
    {
        // 1. On crée d'abord un parent réel
        $parentReel = Emotion::factory()->create(['parent_id' => null, 'niveau' => 1]);

        // 2. On crée l'émotion attachée à ce parent (donc niveau 2)
        $emotion = Emotion::factory()->create([
            'nom' => 'Ancien',
            'parent_id' => $parentReel->id,
            'niveau' => 2
        ]);

        // 3. Action : On veut qu'elle devienne une émotion de base (parent_id = null)
        $data = [
            'nom' => 'Mis à jour',
            'parent_id' => null,
        ];

        $this->repository->update($emotion, $data);

        // 4. Assertions
        $emotion->refresh();
        $this->assertEquals('Mis à jour', $emotion->nom);
        $this->assertNull($emotion->parent_id);
        $this->assertEquals(1, $emotion->niveau);
    }

    /** @test */
    public function update_child_emotion_sets_niveau_to_two()
    {
        $parent = Emotion::factory()->create(['parent_id' => null]);
        $emotion = Emotion::factory()->create(['parent_id' => null, 'niveau' => 1]);

        $data = [
            'nom' => 'SubEmotion',
            'parent_id' => $parent->id,
        ];

        $this->repository->update($emotion, $data);

        $emotion->refresh();
        $this->assertEquals('SubEmotion', $emotion->nom);
        $this->assertEquals($parent->id, $emotion->parent_id);
        $this->assertEquals(2, $emotion->niveau);
    }

    /** @test */
    public function it_deletes_emotion_and_children_in_cascade()
    {
        $parent = Emotion::factory()->create(['parent_id' => null]);
        $child1 = Emotion::factory()->create(['parent_id' => $parent->id]);
        $child2 = Emotion::factory()->create(['parent_id' => $parent->id]);

        $this->repository->deleteWithChildren($parent);

        $this->assertDatabaseMissing('emotions', ['id' => $parent->id]);
        $this->assertDatabaseMissing('emotions', ['id' => $child1->id]);
        $this->assertDatabaseMissing('emotions', ['id' => $child2->id]);
    }

    /** @test */
    public function it_deletes_files_when_emotion_is_deleted()
    {
        Storage::fake('public');

        $parent = Emotion::factory()->create(['parent_id' => null, 'image_icone' => 'emotions/parent.png']);
        $child = Emotion::factory()->create(['parent_id' => $parent->id, 'image_icone' => 'emotions/child.png']);

        Storage::disk('public')->put('emotions/parent.png', 'fake content');
        Storage::disk('public')->put('emotions/child.png', 'fake content');

        $this->repository->deleteWithChildren($parent);

        Storage::disk('public')->assertMissing('emotions/parent.png');
        Storage::disk('public')->assertMissing('emotions/child.png');
    }

    /** @test */
    public function it_can_delete_emotion_without_image()
    {
        $emotion = Emotion::factory()->create(['image_icone' => null]);

        $this->repository->deleteWithChildren($emotion);

        $this->assertDatabaseMissing('emotions', ['id' => $emotion->id]);
    }
}
