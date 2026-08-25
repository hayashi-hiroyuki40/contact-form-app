<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Tag;
class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        Contact::factory(20)->create()->each(function ($contact) use ($tags) {
            $contact->tags()->attach(
                $tags->random(rand(1, 3))
            );
        });
    }
}
