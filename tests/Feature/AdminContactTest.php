<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_未認証ユーザーは管理ダッシュボード表示時にログイン画面へリダイレクトされる(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_認証済みユーザーは管理ダッシュボードを表示できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.index');
    }

    public function test_キーワードで絞り込みができる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create(['first_name' => '山田', 'category_id' => $category->id]);
        Contact::factory()->count(2)->create(['first_name' => '佐藤', 'category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/admin?keyword=山田');

        $this->assertEquals(3, $response->viewData('contacts')->total());
    }

    public function test_性別で絞り込みができる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Contact::factory()->count(4)->create(['gender' => 1, 'category_id' => $category->id]);
        Contact::factory()->count(2)->create(['gender' => 2, 'category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/admin?gender=1');
        $this->assertEquals(4, $response->viewData('contacts')->total());
    }

    public function test_カテゴリで絞り込みができる(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Contact::factory()->count(5)->create(['category_id' => $category1->id]);
        Contact::factory()->count(2)->create(['category_id' => $category2->id]);

        $response = $this->actingAs($user)->get("/admin?category_id={$category1->id}");
        $this->assertEquals(5, $response->viewData('contacts')->total());
    }

    public function test_日付で絞り込みができる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $date = '2026-08-28';

        Contact::factory()->count(2)->create(['created_at' => $date, 'category_id' => $category->id]);
        Contact::factory()->count(3)->create(['created_at' => '2026-08-01', 'category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/admin?date={$date}");
        $this->assertEquals(2, $response->viewData('contacts')->total());
    }

    public function test_検索結果が7件ごとにページネーションされる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Contact::factory()->count(10)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/admin');

        $contacts = $response->viewData('contacts');
        $this->assertCount(7, $contacts);
        $this->assertEquals(10, $contacts->total());
    }

    public function test_指定したお問い合わせがカテゴリ情報付きで詳細ページに表示される(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => '質問']);
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertViewIs('admin.show')
            ->assertSee($contact->email)
            ->assertSee('質問');
    }

    public function test_お問い合わせレコードが正常に削除され管理画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
