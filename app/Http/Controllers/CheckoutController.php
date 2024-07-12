<?php

  namespace App\Http\Controllers;

  use App\Models\Checkout;
  use App\Http\Requests\StoreCheckoutRequest;
  use App\Http\Requests\UpdateCheckoutRequest;
  use App\Models\Event;
  use App\Models\Ticket;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Support\Facades\Auth;
  use Ramsey\Uuid\Uuid;

  class CheckoutController extends Controller
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
    public function store(StoreCheckoutRequest $request, string $event_uuid): JsonResponse
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

      if ($ticket->checkins_count === $ticket->checkouts_count) {
        // If the checkin count is less than or equal to the checkouts count
        // then we can't very well add a new checkout can we?
        return response()->json([
          'message' => 'Ticket has not been checked in!'
        ], 401);
      }

      // Since we've arrive here it is probably safe to insert the "checkin"
      // record now
      $checkout = new Checkout();
      $checkout->ticket()
               ->associate($ticket);
      $checkout->user()
               ->associate(Auth::user());
      $checkout->save();
      $checkout->setVisible([
        'id'
      ]);

      return response()->json($checkout);
    }

    /**
     * Display the specified resource.
     */
    public function show(Checkout $checkout)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCheckoutRequest $request, Checkout $checkout)
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checkout $checkout)
    {
      //
    }
  }
