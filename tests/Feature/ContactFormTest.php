<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせフォームが正常に表示されカテゴリとタグがビューに渡され、ページに表示される(): void
    {
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);
        $tag = Tag::factory()->create(['name' => 'テストタグ']);

        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertViewHas('categories')
            ->assertViewHas('tags')
            ->assertSee('テストカテゴリ')
            ->assertSee('テストタグ');
    }

    public function test_サンクスページが正常に表示される(): void
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
    }

    public function test_確認ページ：バリデーション通過時に入力内容が表示される(): void
    {
        $category = Category::factory()->create(['content' => '質問']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '問い合わせ内容です。',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200)
            ->assertViewIs('contact.confirm')
            ->assertSee('山田')
            ->assertSee('yamada@example.com')
            ->assertSee('質問');
    }

    public function test_確認ページ：必須項目が欠けている場合エラーとなりリダイレクトされる(): void
    {
        $data = [
            'last_name' => '太郎',
            'gender' => 1,
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '問い合わせ内容です。',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['first_name', 'email', 'category_id']);
    }

    public function test_確認ページ：無効なメールアドレス送信時エラーとなりリダイレクトされる(): void
    {
        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'invalid-email-format',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => 1,
            'detail' => '問い合わせ内容です。',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors(['email']);
    }

    public function test_お問い合わせ送信：バリデーション通過時に_d_b保存・タグ記録・リダイレクトされる(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '問い合わせ内容です。',
            'category_name' => $category->name,
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
        ]);

        $contact = Contact::where('email', 'yamada@example.com')->first();
        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_お問い合わせ送信：必須項目省略時はエラーとなりリダイレクトされる(): void
    {
        $data = [
            'last_name' => '太郎',
            'gender' => 1,
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '問い合わせ内容です。',
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors(['first_name', 'email', 'category_id']);
    }

    public function test_お問い合わせ送信：無効なメールアドレス送信時エラーとなりリダイレクトされる(): void
    {
        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'invalid-email-format',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => 1,
            'detail' => '問い合わせ内容です。',
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors(['email']);
    }
}
