<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class IndexContactRequestTest extends TestCase

{
    use RefreshDatabase;

    private function validate(array $data)
    {
        $request = new IndexContactRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_有効な検索フィルタが受け入れられること(): void
    {
        $category = Category::factory()->create();

        $validator = $this->validate([
            'keyword' => '山田',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2024-02-01',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_不正な性別値が拒否されること(): void
    {
        $validator = $this->validate([
            'gender' => 9,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->messages());
    }
}
