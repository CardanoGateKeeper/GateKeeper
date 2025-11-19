<?php

  namespace App\Http\Controllers;

  use App\Http\Resources\TicketResource;
  use App\Models\Checkin;
  use App\Http\Requests\StoreCheckinRequest;
  use App\Http\Requests\UpdateCheckinRequest;
  use App\Models\Event;
  use App\Models\Ticket;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Support\Facades\Auth;
  use Ramsey\Uuid\Uuid;

  class CheckinController extends Controller
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
    public function store(StoreCheckinRequest $request, string $event_uuid): JsonResponse
    {
      $args = $request->validated();

      $event = Event::where('uuid', $event_uuid)
                    ->firstOrFail();

      $ticket = Ticket::where('event_id', $event->id)
                      ->where('ticket_nonce', Uuid::fromString($args['ticket_code'])
                                                  ->getBytes())
                      ->where('asset_id', $args['asset_id'])
                      ->withCount(['checkins', 'checkouts'])
                      ->firstOrFail();

      if ($ticket->checkins_count !== $ticket->checkouts_count) {
        // The only way this should exist is when a ticket has been checked in
        // already and has never been checked out. You can't check-in a ticket
        // twice!!!
        // TODO: Add ability to scan a ticket multiple times to potentially handle
        //       a situation where we want to track a ticket holder's movement
        //       throughout an event
        return response()->json([
          'message' => 'Ticket has already been checked in!'
        ], 400);
      }

      // Since we've arrive here it is probably safe to insert the "checkin"
      // record now
      $checkin = new Checkin();
      $checkin->ticket()
              ->associate($ticket);
      $checkin->user()
              ->associate(Auth::user());
      $checkin->save();
      $checkin->setVisible([
        'id'
      ]);

      return response()->json($checkin);
    }

    /**
     * Display the specified resource.
     */
    public function show(Checkin $checkin)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCheckinRequest $request, Checkin $checkin)
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checkin $checkin)
    {
      //
    }
  }
