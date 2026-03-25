<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index() {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            $categories = Category::all();
        } else {
            $categories = Category::whereNull('created_by')->orWhere('created_by', $user->id)->get();
        }
        return view('categories.index', compact('categories'));
    }

    public function create() {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request) {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();
        Category::create($validated);
        return redirect()->route('categories.index')->with('success', 'Categoria creata!');
    }

    public function edit(Category $category) {
        Gate::authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category) {
        $category->update($request->validated());
        return redirect()->route('categories.index')->with('success', 'Categoria aggiornata!');
    }

    public function destroy(Category $category) {
        Gate::authorize('delete', $category);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoria eliminata!');
    }
}
