<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class StoreContactRequestTest extends TestCase

{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new StoreContactRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_必須項目とタグ入力が受け入れられること(): void
    {
        $category = Category::factory()->create();

        $validator = $this->validate([
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'お問い合わせの本文です。',
            'tags' => [1, 2],
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_不正な電話番号形式が拒否されること(): void
    {
        $validator = $this->validate([
            'tel' => 'invalid-phone-number',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->messages());
    }
}
