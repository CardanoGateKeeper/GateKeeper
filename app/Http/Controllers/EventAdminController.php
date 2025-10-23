<?php

  namespace App\Http\Controllers;

  use App\Http\Requests\StoreEventRequest;
  use App\Http\Requests\UpdateEventRequest;
  use App\Models\Event;
  use App\Models\Policy;
  use Illuminate\Http\Request;
  use Laravel\Jetstream\Jetstream;

  class EventAdminController extends Controller
  {

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $team = $request->user()->currentTeam;

      $events = Event::where('team_id', $team->id)
                     ->withCount([
                       'policies',
                       'tickets',
                     ])
                     ->get()
                     ->setVisible([
                       'uuid',
                       'name',
                       'event_date',
                       'policies_count',
                       'tickets_count',
                     ]);

      return Jetstream::inertia()
                      ->render($request, 'ManageEvent/Index',
                        compact('team', 'events'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
      $team = $request->user()->currentTeam;

      return Jetstream::inertia()
                      ->render($request, 'ManageEvent/Create',
                        compact('team'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {

      $validated = $request->validated();

      if ($validated['event_date']) {
        $validated['event_date'] = date('Y-m-d',
          strtotime($validated['event_date']));
      }

      $Event = Event::create($validated);

      return to_route('manage-event.show', $Event->uuid);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $eventUUID)
    {
      $event = Event::where('uuid', $eventUUID)
                    ->with([
                      'policies',
                      'team.users',
                      'user',
                    ])
                    ->withCount(['tickets', 'checkins', 'checkouts'])
                    ->firstOrFail();

      $event->loadStats();

      return Jetstream::inertia()
                      ->render($request, 'ManageEvent/Show',
                        compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $eventUUID)
    {

      $event = Event::where('uuid', $eventUUID)
                    ->with([
                      'policies',
                      'team',
                      'user',
                    ])
                    ->withCount(['tickets'])
                    ->firstOrFail();

      $team_policies = Policy::where('team_id', $event->team->id)
                             ->get();

      return Jetstream::inertia()
                      ->render($request, 'ManageEvent/Edit',
                        compact('event', 'team_policies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $eventUUID)
    {

      $validated = $request->validated();

      if ($validated['event_date']) {
        $validated['event_date'] = date('Y-m-d',
          strtotime($validated['event_date']));
      }

      $event = Event::where('uuid', $eventUUID)
                    ->firstOrFail();

      $updated = $event->update($validated);

      if ($updated) {
        $request->session()
                ->flash('message', 'Event updated successfully');
      }

      return to_route('manage-event.edit', $event->uuid);


//        dd($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
      //
    }
  }
