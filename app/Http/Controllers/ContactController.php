<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Category;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=Category::all();
        $tags=Tag::all();
    
        return view('contact.index',compact('category','tag'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        if ($request->input('action') === 'back') {
            return redirect()->route('contact.index')->withInput();
        }

        $contact = Contact::create($request->except('tag_ids'));

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->input('tag_ids'));
        }

        return redirect()->route('contact.thanks');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::find($validated['category_id']);

        $tags = collect();
        if (!empty($validated['tag_ids'])) {
            $tags = Tag::whereIn('id', $validated['tag_ids'])->get();
    }

    return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}
