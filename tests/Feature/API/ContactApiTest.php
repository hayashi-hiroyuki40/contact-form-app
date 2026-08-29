<?php

namespace Tests\Feature\Feature\Api;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_一覧取得_ap_i_正常系とバリデーションエラー(): void
    {
        $category = Category::factory()->create();
        Contact::factory()->count(5)->create([
            'category_id' => $category->id,
        ]);

        $this->getJson('/api/v1/contacts')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');

        $this->getJson('/api/v1/contacts?gender=99')
            ->assertStatus(422);
    }

    public function test_詳細取得_ap_i_正常系と404エラー(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '高橋',
            'last_name' => '湊',
            'email' => 'takahashi@example.com',
            'gender' => 2,
        ]);

        $this->getJson("/api/v1/contacts/{$contact->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $contact->id,
                    'first_name' => '高橋',
                    'last_name' => '湊',
                    'email' => 'takahashi@example.com',
                    'gender' => 2,
                ],
            ]);

        $this->getJson('/api/v1/contacts/999')
            ->assertNotFound();
    }

    public function test_作成_ap_i_正常系とバリデーションエラー(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $date = [
            'first_name' => '山田',
            'last_name' => '弘毅',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09000000000',
            'address' => '東京都',
            'detail' => '問合せ内容',
            'category_id' => $category->id,
            'tags' => [$tag->id],
        ];

        $this->postJson('/api/v1/contacts', $date)
            ->assertStatus(201);

        $this->assertDatabaseHas('contacts', ['email' => 'yamada@example.com']);

        $this->postJson('/api/v1/contacts', [])
            ->assertStatus(422);
    }

    public function test_更新_ap_i_正常系と404エラー(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '前太郎',
        ]);

        $updatedate = [
            'first_name' => '後太郎',
            'last_name' => $contact->last_name,
            'gender' => $contact->gender,
            'email' => $contact->email,
            'tel' => $contact->tel,
            'address' => $contact->address,
            'detail' => $contact->detail,
            'category_id' => $contact->category_id,
        ];

        $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $updatedate
        )
            ->assertStatus(200);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '後太郎',
        ]);

        $this->putJson('/api/v1/contacts/999', [])
            ->assertNotFound();
    }

    public function test_削除_ap_i_正常系と404エラー(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->deleteJson("/api/v1/contacts/{$contact->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);

        $this->deleteJson('/api/v1/contacts/999')
            ->assertNotFound();
    }
}
