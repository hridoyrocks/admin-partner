<?php

namespace App\Http\Controllers;

use App\Models\CallTopic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopicController extends Controller
{
    public function index()
    {
        $topics = CallTopic::orderBy('id', 'desc')->get();

        return Inertia::render('Topics/Index', [
            'topics' => $topics,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        CallTopic::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Topic created successfully');
    }

    public function update(Request $request, $id)
    {
        $topic = CallTopic::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $topic->update($request->only(['title', 'description']));

        return back()->with('success', 'Topic updated successfully');
    }

    public function destroy($id)
    {
        $topic = CallTopic::findOrFail($id);
        $topic->delete();

        return back()->with('success', 'Topic deleted successfully');
    }
}
