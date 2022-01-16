<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable= ['name', 'slug', 'parent_id', 'description'];

    /**
     * return parent for many categories
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo($this, 'parent_id');
    }

    /**
     * that method to return children categories
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function children()
    {
        return $this->hasMany($this, 'parent_id');
    }
}
