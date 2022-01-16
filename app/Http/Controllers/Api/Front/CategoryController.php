<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CategoryRequest;
use App\Models\Category;
use App\Transformers\Front\CategoryTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $skip = ($request->has('skip')) ? $request->skip : 0;
        $categories = new Category();
        // Search by category name
        if ($request->has('q')) {
            $categories = $categories->where('name', 'LIKE',"%{$request->q}%");
        }
        // Filter by parent_id
        if($request->has('parent_id')){
            $categories = $categories->where('parent_id', $request->parent_id);
        }
        // filter by slug
        if($request->has('slug')){
            $categories = $categories->where('slug', $request->slug);
        }
        $count = $categories->count();
        if ($request->has('skip')) {
            $categories = $categories->orderBy('created_at', 'DESC')->take(10)->skip($skip)->get();
        } else {
            $categories = $categories->orderBy('created_at', 'DESC')->get();
        }
        $fractal = fractal()
            ->collection($categories)
            ->transformWith(new CategoryTransformer())
            ->includeParent()
            ->includeChildren()
            ->toArray();
        return $this->ResponseApi("", $fractal, 200,['count' => $count]);
    }

    /**
     * store new category
     * @param CategoryRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->input('name'),
            'slug'=> Str::slug($request->input('name')),
            'parent_id' => $request->input('parent_id'),
            'description' => $request->input('description')
        ]);
        $fractal = fractal()
            ->item($category)
            ->transformWith(new CategoryTransformer())
            ->includeParent()
            ->includeChildren()
            ->toArray();
        return $this->ResponseApi('Category Created Successfully', $fractal);
    }

    /**
     * show category by id
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        $fractal = fractal()
            ->item($category)
            ->transformWith(new CategoryTransformer())
            ->includeParent()
            ->includeChildren()
            ->toArray();
        return $this->ResponseApi("", $fractal);
    }


    /**
     * update existing category by id
     * @param CategoryRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->input('name') ?? $category->name,
            'slug' => Str::slug($request->input('name')) ?? $category->slug,
            'parent_id' => $request->input('parent_id') ?? $category->parent_id
        ]);
        $fractal = fractal()
            ->item($category)
            ->transformWith(new CategoryTransformer())
            ->includeParent()
            ->includeChildren()
            ->toArray();
        return $this->ResponseApi('Category Updated Successfully', $fractal);
    }

    /**
     * Delete existing category by id
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $this->ResponseApi('Category Deleted Successfully');
    }
}
