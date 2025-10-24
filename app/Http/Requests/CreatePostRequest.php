<?php

namespace App\Http\Requests;

/**
 * @property string $title
 * @property string $post_content
 * @property bool   $is_published
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
            'title' => 'required|string|max:255',
            'post_content' => 'required|string|min:10',
            'is_published' => 'required|boolean',
        ];
    }
}
