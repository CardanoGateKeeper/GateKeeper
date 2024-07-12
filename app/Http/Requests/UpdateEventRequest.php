<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest {

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
            'name'                    => 'required|string',
            'nonce_valid_for_minutes' => 'required|integer|min:1',
            'hodl_asset'              => 'required|boolean',
            'start_date_time'         => 'nullable|date',
            'end_date_time'           => 'nullable|date',
            'location'                => 'nullable|string',
            'event_start'             => 'nullable|string',
            'event_end'               => 'nullable|string',
            'event_date'              => 'nullable|date',
        ];
    }

    /**
     * Make sure that the User ID and Team ID are always set to the current
     * values for the existing user and selected team so they can't be spoofed
     * in the request body itself.
     *
     * @return void
     */
    protected function prepareForValidation(): void {
        /*$this->merge([
            'user_id'    => $this->user()->id,
            'team_id'    => $this->user()->currentTeam->id,
//            'event_date' => date('Y-m-d', strtotime($this->event_date)),
        ]);*/
    }
}
