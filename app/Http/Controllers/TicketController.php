<?php

  namespace App\Http\Controllers;

  use App\Http\Resources\TicketResource;
  use App\Models\Event;
  use App\Models\Ticket;
  use App\Http\Requests\StoreTicketRequest;
  use App\Http\Requests\UpdateTicketRequest;
  use App\Sidecar\SignData;
  use App\Sidecar\SignTxn;
  use Illuminate\Support\Str;
  use Ramsey\Uuid\Uuid;

  class TicketController extends Controller
  {

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
      $details = $request->validated();

      $event = Event::where('uuid', $details['event_uuid'])
                    ->firstOrFail();

      if (!$event->isticketingactive()) {
        return response()->json([
          'message' => 'Sorry, ticketing is closed for this event.'
        ], 409);
      }

      $policy = $event->policies()
                      ->where('hash', $details['policy_id'])
                      ->firstOrFail();


      $ticket = Ticket::where('event_id', $event->id)
                      ->where('policy_id', $policy->id)
                      ->where('asset_id', $details['asset_id'])
                      ->where('stake_key', $details['stake_key'])
                      ->latest('id')
                      ->first();

      if ($ticket) {
        /**
         * TODO: Check if the existing ticket is already checked in or not
         * If the ticket is already checked in, we need to throw an error
         *
         * TODO: Check if the existing ticket's signBy is expired
         * If the ticket's signBy is expired then we should delete it and create
         * a new one
         */
      }

      if (!$ticket) {
        // Generate a new ticket
        $ticket = new Ticket;
        $ticket->fill([
          ...$details,
          'event_id' => $event->id,
          'policy_id' => $policy->id,
          'signature_nonce' => Str::uuid()
                                  ->getBytes()
        ]);

        $ticket->save();
      }

      $signature_json = $ticket->generate_signing_json();


      // Uuid::fromBytes($this->signature_nonce)
      //                                 ->toString()

      return [
        'id' => Uuid::fromBytes($ticket->signature_nonce)
                    ->toString(),
        'nonce' => bin2hex($signature_json),
      ];

    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, string $ticket_nonce): mixed
    {
      $details = $request->validated();

      $event = Event::where('uuid', $details['event_uuid'])
                    ->firstOrFail();

      if (!$event->isticketingactive()) {
        return false;
      }

      $policy = $event->policies()
                      ->where('hash', $details['policy_id'])
                      ->firstOrFail();

      $ticket = Ticket::where('event_id', $event->id)
                      ->where('policy_id', $policy->id)
                      ->where('asset_id', $details['asset_id'])
                      ->where('stake_key', $details['stake_key'])
                      ->where('signature_nonce', Uuid::fromString($ticket_nonce)
                                                     ->getBytes())
                      ->firstOrFail();


      if (!empty($details['signature']['txn'])) {
        $details['signatureMethod'] = true;

        $valid_signature = SignTxn::execute([
          'tx_hex' => $details['signature']['txn'],
          'stake_key' => $ticket->stake_key,
          'nonce' => bin2hex($ticket->generate_signing_json()),
        ])
                                  ->body();
      } else {
        $details['signatureMethod'] = false;

        $valid_signature = SignData::execute([
          'signature' => $details['signature']['signature'],
          'key' => $details['signature']['key'],
          'payload' => bin2hex($ticket->generate_signing_json()),
          'stake_address' => $ticket->stake_key,
          'network_mode' => 'mainnet'
        ])
                                   ->body();
      }

      $details['valid_signature'] = $valid_signature;

      if (!$valid_signature) {
        return response()->json([
          'message' => 'Sorry, that signature is invalid!'
        ], 401);
      }

      // Validate that the user holds the asset here...
      // By doing it so far down in the process we minimize the number of
      // false-requests we need to make to Blockfrost, Koios or another data
      // provider.

      if (empty($ticket->ticket_nonce)) {
        $ticket->ticket_nonce = Uuid::uuid4()
                                    ->getBytes();
        $ticket->signature = json_encode($details['signature']);
        $ticket->save();
      }

      $ticket->removeOldAttempts();

      $ticket_nonce = Uuid::fromBytes($ticket->ticket_nonce)
                          ->toString();

      return [
        'qr_value' => $ticket->asset_id . '|' . $ticket_nonce,
        'security_code' => $ticket_nonce,
      ];

//      return $details;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
      //
    }
  }
