<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    protected function onCreate()
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'sometimes|nullable|integer',
            'description' => 'sometimes|nullable|string'
        ];
    }
    protected function onUpdate()
    {
        return [
            'name' => 'sometimes|nullable|string',
            'parent_id' => 'sometimes|nullable|integer',
            'description' => 'sometimes|nullable|string'
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return request()->isMethod('put') || request()->isMethod('patch') ?
            $this->onUpdate() : $this->onCreate();
    }
}
