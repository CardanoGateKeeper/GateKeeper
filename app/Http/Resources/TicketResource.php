<?php

  namespace App\Http\Resources;

  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;
  use Illuminate\Support\Str;
  use Ramsey\Uuid\Uuid;

  class TicketResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        'id' => $this->id,
        'event_id' => $this->event_id,
        'policy_id' => $this->policy_id,
        'asset_id' => $this->asset_id,
        'stake_key' => $this->stake_key,
        'signature_nonce' => Uuid::fromBytes($this->signature_nonce)
                                 ->toString(),
        'ticket_nonce' => $this->when($this->ticket_nonce != null, function () {
          return Uuid::fromBytes($this->ticket_nonce)
                     ->toString();
        }),
        'count' => [
          'checkins' => $this->whenCounted('checkins'),
          'checkouts' => $this->whenCounted('checkouts')
        ]
      ];
    }
  }
