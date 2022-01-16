<?php

namespace App\Transformers\Front;

use App\Models\Category;
use League\Fractal\TransformerAbstract;
use phpDocumentor\Reflection\Types\Self_;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
      'parent', 'children'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'id' => (int) $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'description'=> $category->description
        ];
    }
    public function includeParent(Category $category)
    {
        $parent = $category->parent;
        if(!$parent){
            return $this->null();
        }
        return $this->item($category->parent, new CategoryTransformer);
    }
    public function includeChildren(Category $category)
    {
        return $this->collection($category->children, new CategoryTransformer);
    }

}
