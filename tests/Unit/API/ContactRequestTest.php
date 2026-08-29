<?php

namespace Tests\Unit\Unit\API;

use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_ap_i検索バリデーションで有効な入力値を許可する(): void
    {
        $category = Category::factory()->create();

        $data = [
            'keyword' => 'テスト',
            'gender' => 2,
            'category_id' => $category->id,
            'date' => '2026-08-29',
            'per_page' => 15,
        ];

        $request = new IndexContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_ap_i検索バリデーションで不正な値を拒否する(): void
    {
        $dummyData = [
            'gender' => 4,
            'date' => 'dummy-date',
            'per_page' => 'not-number',
        ];

        $request = new IndexContactRequest;
        $validator = Validator::make($dummyData, $request->rules());

        $this->assertTrue($validator->fails());
    }

    public function test_ap_i作成バリデーションで全必須項目とタグ入力を許可する(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'first_name' => '三宅',
            'last_name' => '結衣',
            'gender' => 2,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区...',
            'detail' => 'お問い合わせ内容詳細',
            'category_id' => $category->id,
            'tags' => [$tag->id],
        ];

        $request = new StoreContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_ap_i作成バリデーションで必須項目不足や不正な形式を拒否する(): void
    {
        $data = [
            'first_name' => '',
            'email' => 'not-email',
            'gender' => 99,
        ];

        $request = new StoreContactRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
    }
}
