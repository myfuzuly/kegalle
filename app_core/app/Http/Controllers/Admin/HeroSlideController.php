<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('sort_order')->get();
        return view('admin.hero-slides.index', compact('slides'));
    }

    public function store(Request $request)
    {
        // Diagnose upload errors before validation
        if ($request->hasFile('image')) {
            $errCode = $request->file('image')->getError();
            if ($errCode !== UPLOAD_ERR_OK) {
                $errMap = [1=>'INI_SIZE',2=>'FORM_SIZE',3=>'PARTIAL',6=>'NO_TMP_DIR',7=>'CANT_WRITE',8=>'EXTENSION'];
                return back()->withErrors(['image' => 'PHP upload error ' . $errCode . ' (' . ($errMap[$errCode] ?? 'unknown') . '). File size: ' . ($_FILES['image']['size'] ?? '?') . ' bytes.']);
            }
        }
        $data = $request->validate([
            'image'       => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'title'       => ['nullable', 'string', 'max:120'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        try {
            $file = $request->file('image');
            $dir  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/') . '/images/hero-slides';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext      = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
            $filename = 'slide-' . uniqid() . '.' . $ext;
            $absPath  = $dir . DIRECTORY_SEPARATOR . $filename;
            // Use file_put_contents to avoid move_uploaded_file restrictions
            if (file_put_contents($absPath, file_get_contents($file->getRealPath())) === false) {
                throw new \RuntimeException('file_put_contents failed for: ' . $absPath);
            }
            \App\Helpers\ImageHelper::resizeDown($absPath, 2000);
            $path = '/images/hero-slides/' . $filename;
        } catch (\Throwable $e) {
            return back()->withErrors(['image' => 'Upload failed: ' . $e->getMessage()]);
        }

        HeroSlide::create([
            'image'      => $path,
            'title'      => $data['title'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        Cache::forget('hero_slides');
        return back()->with('success', 'Slide added successfully.');
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $request->validate([
            'image'      => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'title'      => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $payload = [
            'title'      => $data['title'] ?? $heroSlide->title,
            'sort_order' => $data['sort_order'] ?? $heroSlide->sort_order,
            'is_active'  => $request->boolean('is_active', $heroSlide->is_active),
        ];

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $dir  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/') . '/images/hero-slides';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $ext      = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
                $filename = 'slide-' . uniqid() . '.' . $ext;
                $absPath  = $dir . DIRECTORY_SEPARATOR . $filename;
                if (file_put_contents($absPath, file_get_contents($file->getRealPath())) === false) {
                    throw new \RuntimeException('file_put_contents failed for: ' . $absPath);
                }
                \App\Helpers\ImageHelper::resizeDown($absPath, 2000);
                $payload['image'] = '/images/hero-slides/' . $filename;
                // Delete old file if it's in our hero-slides folder
                if ($heroSlide->image && str_starts_with($heroSlide->image, '/images/hero-slides/')) {
                    @unlink(rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/') . '/' . ltrim($heroSlide->image, '/'));
                }
            } catch (\Throwable $e) {
                return back()->withErrors(['image' => 'Upload failed: ' . $e->getMessage()]);
            }
        }

        $heroSlide->update($payload);
        Cache::forget('hero_slides');
        return back()->with('success', 'Slide updated.');
    }

    public function toggle(HeroSlide $heroSlide)
    {
        $heroSlide->update(['is_active' => !$heroSlide->is_active]);
        Cache::forget('hero_slides');
        return back()->with('success', 'Slide ' . ($heroSlide->is_active ? 'hidden' : 'shown') . '.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image && str_starts_with($heroSlide->image, '/images/hero-slides/')) {
            @unlink(rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/') . '/' . ltrim($heroSlide->image, '/'));
        }
        $heroSlide->delete();
        Cache::forget('hero_slides');
        return back()->with('success', 'Slide deleted.');
    }
}
