<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePolicyRequest;
use App\Models\Policy;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamPolicyController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index(Team $team) {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Team $team) {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePolicyRequest $request,
                          Team               $team): RedirectResponse {
        $validated = $request->validated();

        $team->policies()
             ->create($validated);

        return back(303);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team, Policy $policy) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team, Policy $policy) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team, Policy $policy) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team, Policy $policy) {
        //
    }
}
