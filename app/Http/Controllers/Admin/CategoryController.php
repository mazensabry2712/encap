<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['remove_gallery']);
        $data['image'] = $request->file('image')?->store('categories', 'public');
        $data['gallery'] = collect($request->file('gallery', []))
            ->map(fn ($file) => $file->store('categories/gallery', 'public'))->all();
        $data['is_published'] = $request->boolean('is_published');
        $data['available_forms'] = array_values(array_intersect(
            $request->input('available_forms', []),
            ['Fresh', 'Frozen', 'Processed']
        ));

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', __('Category created.'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        unset($data['remove_gallery']);

        $oldImage = $category->image;
        $newImage = $request->file('image')?->store('categories', 'public');
        if ($newImage) {
            $data['image'] = $newImage;
        }

        $gallery = $category->gallery ?? [];
        $removedPaths = [];
        foreach ($request->input('remove_gallery', []) as $remove) {
            if (array_key_exists($remove, $gallery)) {
                $removedPaths[] = $gallery[$remove];
                unset($gallery[$remove]);
            }
        }

        foreach ($request->file('gallery', []) as $file) {
            $gallery[] = $file->store('categories/gallery', 'public');
        }

        $data['gallery'] = array_values($gallery);
        $data['is_published'] = $request->boolean('is_published');
        $data['available_forms'] = array_values(array_intersect(
            $request->input('available_forms', []),
            ['Fresh', 'Frozen', 'Processed']
        ));

        $category->update($data);

        $pathsToDelete = $removedPaths;
        if ($newImage && $oldImage) {
            $pathsToDelete[] = $oldImage;
        }
        Storage::disk('public')->delete($pathsToDelete);

        return redirect()->route('admin.categories.index')->with('success', __('Category updated.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $pathsToDelete = array_filter(array_merge([$category->image], $category->gallery ?? []));
        $category->delete();
        Storage::disk('public')->delete($pathsToDelete);

        return back()->with('success', __('Category deleted.'));
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:160'],
            'name_ar' => ['nullable', 'string', 'max:160'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'short_description_en' => ['nullable', 'string', 'max:500'],
            'short_description_ar' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'varieties_en' => ['nullable', 'string', 'max:2000'],
            'varieties_ar' => ['nullable', 'string', 'max:2000'],
            'harvest_window_en' => ['nullable', 'string', 'max:255'],
            'harvest_window_ar' => ['nullable', 'string', 'max:255'],
            'specifications_en' => ['nullable', 'string', 'max:2000'],
            'specifications_ar' => ['nullable', 'string', 'max:2000'],
            'packaging_en' => ['nullable', 'string', 'max:255'],
            'packaging_ar' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string', 'max:500'],
            'seo_description_ar' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', 'min:0'],
            'available_forms' => ['nullable', 'array'],
            'available_forms.*' => ['in:Fresh,Frozen,Processed'],
        ]);
    }
}
