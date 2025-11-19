<?php

  namespace App\Http\Controllers;

  use App\Models\Event;
  use Illuminate\Http\Request;
  use Laravel\Jetstream\Jetstream;

  class EventCheckinController extends Controller
  {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $team = $request->user()->currentTeam;

      $events = Event::where('team_id', $team->id)
                     ->get()
                     ->setVisible([
                       'uuid', 'name', 'event_date'
                     ]);

      return Jetstream::inertia()
                      ->render($request, 'ScanTickets/Index',
                        compact('team', 'events'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $event_uuid)
    {
      $event = Event::where('uuid', $event_uuid)
                    ->firstOrFail();

      return Jetstream::inertia()
                      ->render($request, 'ScanTickets/Show',
                        compact('event'));
    }
  }
