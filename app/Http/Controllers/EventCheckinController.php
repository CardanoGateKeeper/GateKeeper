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
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      //
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      //
    }
  }
