<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Policy;
use Illuminate\Http\Request;
use Throwable;

class EventPolicyController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event) {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event) {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $eventUUID) {
        // Here we want to attach the provided Policy to the event...
        $event = Event::where([
            'uuid' => $eventUUID,
        ])
                      ->firstOrFail();

        $policy = Policy::where([
            'id'      => $request->policy_id,
            'team_id' => $event->team_id,
        ])
                        ->firstOrFail();
        try {
            $event->policies()
                  ->attach($policy->id);
        } catch (Throwable $e) {
            // Do nothing
        }

        return back(303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Policy $policy) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, Policy $policy) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event, Policy $policy) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $eventUUID,
                            Policy  $policy) {
        $event = Event::where([
            'uuid' => $eventUUID,
        ])
                      ->firstOrFail();

        try {
            $event->policies()
                  ->detach($policy->id);
        } catch (Throwable $e) {
            // Do nothing
        }

        return back(303);
    }
}
