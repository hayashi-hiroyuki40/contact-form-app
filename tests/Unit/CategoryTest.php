<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
class CategoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_モデル_カテゴリ関係_1つのカテゴリから紐づく複数のお問い合わせが取得できること()
    {
        $category = Category::factory()->create();
        $contacts = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->contacts);
        $this->assertEquals($contacts->first()->id, $category->contacts->first()->id);
    }
}
