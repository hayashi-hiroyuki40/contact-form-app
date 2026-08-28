<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_モデル_お問い合わせ関係_1つのお問い合わせが特定のカテゴリに属すること()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $contact->category);
        $this->assertEquals($category->id, $contact->category->id);
    }

    public function test_モデル_お問い合わせ関係_1つのお問い合わせが複数のタグと紐づくこと()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);
        $tags = Tag::factory()->count(2)->create();

        $contact->tags()->attach($tags->pluck('id'));

        $this->assertCount(2, $contact->tags);

        $this->assertEquals($tags->first()->id, $contact->tags->first()->id);
    }

    public function test_モデル_お問い合わせ関係_複数のタグと同期できること()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);
        $tags = Tag::factory()->count(2)->create();

        $contact->tags()->sync($tags->pluck('id'));

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tags->first()->id, ]);
    }
}
