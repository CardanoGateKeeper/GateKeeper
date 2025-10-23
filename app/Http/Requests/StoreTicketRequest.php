<?php

  namespace App\Http\Requests;

  use Illuminate\Foundation\Http\FormRequest;

  class StoreTicketRequest extends FormRequest
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
      // Policy ID
      // 34d825881c5a6465d0398dbbe301222427d3572f31ba36148e89ce54
      // Stake Keys
      // stake1u9qt5vuj78whfms38csd775khet8guqet3xl82lhmkmytpq2ktzhc
      // stake_test1u9qt5vuj78whfms38csd775khet8guqet3xl82lhmkmytpq2ktzhc
      return [
        'event_uuid' => 'required|uuid',
        'policy_id' => 'required|string|size:56',
        'asset_id' => 'required|string|max:32',
        'stake_key' => 'required|string|max:64|min:59'
      ];
    }
  }
