<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $category = new Category();
        $category->fill($request->all());
        $category->save();

        return redirect('admin/dashboard')
            ->with('success', 'Category created');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category): \Illuminate\Http\RedirectResponse
    {

        if (isset($request['category_title'])){
            $category->title = $request['category_title'];
            $category->save();

            return redirect('admin/dashboard')
                ->with('success', 'Category title changed')
                ;
        }

        if (isset($request['category_description'])){
            $category->description = $request['category_description'];
            $category->save();

            return redirect('admin/dashboard')
                ->with('success', 'Category description changed')
                ;
        }

        return redirect('admin/dashboard')
            ->with('error', 'Couldn\'t update anything on category')
            ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        $category->delete();
        return redirect('admin/dashboard')
            ->with('success', 'Category deleted');
    }
}
