<?php

  namespace App\Http\Requests;

  use Illuminate\Foundation\Http\FormRequest;

  class UpdateTicketRequest extends FormRequest
  {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return [
        'event_uuid' => 'required|uuid',
        'policy_id' => 'required|string|size:56',
        'asset_id' => 'required|string|max:32',
        'stake_key' => 'required|string|max:64|min:59',
        'nonce' => 'required|string',
        'signature' => 'required'
      ];
    }
  }
