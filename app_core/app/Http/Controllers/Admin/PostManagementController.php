<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostManagementController extends Controller
{
    public function index()
    {
        $posts = Post::orderByDesc('published_at')->orderByDesc('id')->paginate(30);

        return view('admin.posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'slug' => 'nullable|string|max:200|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
            'is_published' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:320',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
            $path = \App\Helpers\ImageHelper::finalize($path);
        }

        Post::create([
            'title' => $data['title'],
            'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'body' => HtmlSanitizer::clean($data['body'] ?? null),
            'image' => $path,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? now() : null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return back()->with('success', 'Article created successfully.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'slug' => ['required', 'string', 'max:200', Rule::unique('posts', 'slug')->ignore($post->id)],
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
            'is_published' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:320',
        ]);

        $path = $post->image;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
            $path = \App\Helpers\ImageHelper::finalize($path);
        }

        $wasPublished = (bool) $post->is_published;
        $isPublished = $request->boolean('is_published');

        $post->update([
            'title' => $data['title'],
            'slug' => Str::slug($data['slug']),
            'excerpt' => $data['excerpt'] ?? null,
            'body' => HtmlSanitizer::clean($data['body'] ?? null),
            'image' => $path,
            'is_published' => $isPublished,
            'published_at' => $isPublished && ! $wasPublished ? now() : ($isPublished ? $post->published_at ?? now() : $post->published_at),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return redirect('/admin/posts')->with('success', 'Article updated successfully.');
    }

    public function toggle(Post $post)
    {
        $post->is_published = ! (bool) $post->is_published;
        if ($post->is_published && ! $post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        return back()->with('success', 'Article status updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'Article deleted successfully.');
    }
}
