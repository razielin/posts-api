<?php

namespace App\Http\Requests;

/**
 * @property string|null $title
 * @property string|null $post_content
 * @property bool|null   $is_published
 */
class EditPostRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'post_content' => 'sometimes|required|string|min:10',
            'is_published' => 'sometimes|required|boolean',
        ];
    }
}
