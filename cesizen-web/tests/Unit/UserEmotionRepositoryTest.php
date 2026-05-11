<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Emotion;
use App\Models\UserEmotion;
use App\Models\Role;
use App\Repositories\UserEmotionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserEmotionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserEmotionRepository();
        Role::create(['id' => 1, 'libelle' => 'Administrateur']);
        Role::create(['id' => 2, 'libelle' => 'Utilisateur']);
    }

    /** @test */
    public function it_can_store_a_user_emotion()
    {
        $user = User::factory()->create();
        $emotion = Emotion::create(['nom' => 'Stress']);

        $result = $this->repository->store($user->id, $emotion->id);

        $this->assertInstanceOf(UserEmotion::class, $result);
        $this->assertEquals($emotion->id, $result->emotion_id);
        $this->assertDatabaseHas('user_emotions', [
            'user_id' => $user->id,
            'emotion_id' => $emotion->id
        ]);
    }

    /** @test */
    public function it_can_delete_only_owned_emotion()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $emotion = Emotion::create(['nom' => 'Neutre']);
        
        $entry = UserEmotion::create(['user_id' => $otherUser->id, 'emotion_id' => $emotion->id]);

        // Tentative de suppression par l'utilisateur qui n'est pas propriétaire
        $deleted = $this->repository->delete($user->id, $entry->id);

        // delete() retourne le nombre de lignes affectées, ici 0
        $this->assertEquals(0, $deleted);
        $this->assertDatabaseHas('user_emotions', ['id' => $entry->id]);
    }

    /** @test */
    public function it_returns_correct_stats_grouped_by_emotion_name()
    {
        $user = User::factory()->create();
        $emo1 = Emotion::create(['nom' => 'Joie']);
        $emo2 = Emotion::create(['nom' => 'Triste']);

        UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emo1->id]);
        UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emo1->id]);
        UserEmotion::create(['user_id' => $user->id, 'emotion_id' => $emo2->id]);

        $stats = $this->repository->getStats($user->id);

        // On vérifie qu'on a bien 2 groupes
        $this->assertCount(2, $stats);
        
        // On vérifie le compte pour 'Joie'
        $joieStat = $stats->where('nom', 'Joie')->first();
        $this->assertEquals(2, $joieStat->total);
    }
}