<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePolicyRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'name'    => [
                'required',
                'string',
                Rule::unique('policies')
                    ->where(fn($query) => $query->where('team_id',
                        $this->user()->currentTeam->id)),
            ],
            'hash'    => [
                'required',
                'string',
                'min:56',
                'max:56',
                Rule::unique('policies')
                    ->where(fn($query) => $query->where('team_id',
                        $this->user()->currentTeam->id)),
            ],
            'team_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'user_id' => $this->user()->id,
            'team_id' => $this->user()->currentTeam->id,
        ]);
    }
}
