<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {
        $query = Contact::query();

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('last_name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('email', 'like', '%'.$request->keyword.'%');
            });
        }

        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->with('category','tags')
            ->latest()
            ->paginate(7)
            ->appends($request->validated());

        $categories = Category::all();

        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories','tags'));
    }

    public function show(Contact $contact)
    {
        $contact->with('category');

        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect('/admin');
    }
}
