<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->except('tag_ids'));

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->input('tag_ids'));
        }

        return redirect('/thanks');
    }

    public function confirm(StoreContactRequest $request)
    {
        $request->flash();

        $validated = $request->validated();

        $category = Category::find($validated['category_id']);

        $tags = collect();

        if (! empty($validated['tag_ids'])) {
            $tags = Tag::whereIn('id', $validated['tag_ids'])->get();
        }

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}
