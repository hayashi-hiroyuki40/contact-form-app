<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_モデル_タグ関係_中間テーブルを介して1つのタグが複数のお問い合わせに紐づいていること()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $contacts = Contact::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        $tag->contacts()->attach($contacts->pluck('id'));

        $this->assertCount(2, $tag->contacts);

        $this->assertEquals($contacts->first()->id, $tag->contacts->first()->id);
    }

    public function test_バリデーション_タグ新規登録_タグ名の必須入力文字数制限一意性が維持されていること()
    {
        $this->expectException(QueryException::class);

        Tag::factory()->create(['name' => '重複タグ']);
        Tag::factory()->create(['name' => '重複タグ']);
    }

    public function test_バリデーション_タグ更新_自身の名前維持は可能だが他で使用済みのタグ名への変更は拒否すること()
    {
        $tag1 = Tag::factory()->create(['name' => 'タグ1']);
        $tag2 = Tag::factory()->create(['name' => 'タグ2']);

        $this->assertTrue($tag1->update(['name' => 'タグ1']));

        $this->expectException(QueryException::class);
        $tag1->update(['name' => 'タグ2']);
    }
}
