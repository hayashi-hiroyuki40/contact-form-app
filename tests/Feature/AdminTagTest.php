<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証済みユーザーはタグの編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($user)
            ->get("/admin/tags/{$tag->id}/edit")
            ->assertStatus(200);
    }

    public function test_認証済みユーザーはタグを作成でき管理画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/tags', ['name' => '新規タグ']);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);
    }

    public function test_認証済みユーザーはタグを更新でき管理画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '旧タグ']);

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", ['name' => '更新タグ']);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => '更新タグ']);
    }

    public function test_認証済みユーザーはタグを削除でき管理画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_未認証ユーザーはタグ操作が拒否されログイン画面へリダイレクトされる(): void
    {
        $tag = Tag::factory()->create();

        $this->get("/admin/tags/{$tag->id}/edit")->assertRedirect('/login');
        $this->post('/admin/tags', ['name' => 'テスト'])->assertRedirect('/login');
        $this->put("/admin/tags/{$tag->id}", ['name' => 'テスト'])->assertRedirect('/login');
        $this->delete("/admin/tags/{$tag->id}")->assertRedirect('/login');
    }
}
