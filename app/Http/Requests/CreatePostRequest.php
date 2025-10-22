<?php

namespace App\Http\Requests;

/**
 * @property string $title
 * @property string $content
 * @property bool|null $is_published
 */
class CreatePostRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string|min:10',
            'is_published' => 'required|boolean',
        ];
    }
}
