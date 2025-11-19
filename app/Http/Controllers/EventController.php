<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;

class EventController extends Controller
{
  /**
   * List public, discoverable events.
   *
   * Returns upcoming public events along with their associated Cardano policy IDs.
   * Clients can use this to find events that a connected wallet might qualify for.
   *
   * @group Public API
   * @unauthenticated
   *
   * @queryParam policy_hashes[] string[] Optional. One or more Cardano policy IDs to filter by.
   *                                      Only events that accept at least one of these policies
   *                                      will be returned.
   *                                      Example: policy_hashes[]=a0028f35...&policy_hashes[]=40fa2aa6...
   *
   * @response 200 scenario="success" {
   *   "data": [
   *     {
   *       "uuid": "8f5fa03d-02c6-49f1-985f-d71544de8919",
   *       "name": "Hydra Launch Party",
   *       "description": "An exclusive Hydra-based launch event with live demos.",
   *       "location": "Las Vegas Convention Center",
   *       "date": "2025-01-24",
   *       "start": "2025-01-24T18:00:00Z",
   *       "end": "2025-01-24T21:00:00Z",
   *       "policies": [
   *         {
   *           "hash": "a0028f35d4c7b4f2f0b1d6a0e4c3a9f6d0b0cafe",
   *           "name": "HOSKY Token"
   *         },
   *         {
   *           "hash": "40fa2aa65d983e1375f1a7c61ba57c0f0f5b9abc",
   *           "name": "Clay Nation"
   *         }
   *       ],
   *       "policy_hashes": [
   *         "a0028f35d4c7b4f2f0b1d6a0e4c3a9f6d0b0cafe",
   *         "40fa2aa65d983e1375f1a7c61ba57c0f0f5b9abc"
   *       ]
   *     }
   *   ]
   * }
   *
   * @responseField data[].uuid string The event UUID used in app links.
   * @responseField data[].name string The display name of the event.
   * @responseField data[].description string|null A short description of the event.
   * @responseField data[].location string|null The event venue or location.
   * @responseField data[].date string|null Event date in Y-m-d format.
   * @responseField data[].start string|null ISO8601 start datetime.
   * @responseField data[].end string|null ISO8601 end datetime.
   * @responseField data[].policies[].hash string Cardano policy ID accepted for this event.
   * @responseField data[].policies[].name string Human-readable label for the policy.
   * @responseField data[].policy_hashes string[] Convenience array of all policy hashes for this event.
   */
  public function index(Request $request): JsonResponse
  {


    $policyHashes = $request->input('policy_hashes', []);

    if (is_array($policyHashes)) {
      $policyHashes = array_values(array_filter($policyHashes));
      sort($policyHashes); // order-independent
    } else {
      $policyHashes = [$policyHashes];
    }

    $cacheKey = $this->buildCacheKey($policyHashes);

    $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($policyHashes) {
      $now = Carbon::now();

      $query = Event::query()
        ->where('is_public', true)
        ->where(function ($q) use ($now) {
          // Upcoming or currently-running: not ended yet
          $q->whereNull('end_date_time')
            ->orWhere('end_date_time', '>=', $now);
        })
        ->with([
          'policies:id,hash,name',
        ])
        ->orderBy('start_date_time');

      if (!empty($policyHashes) && is_array($policyHashes)) {
        $query->whereHas('policies', function ($q) use ($policyHashes) {
          $q->whereIn('hash', $policyHashes);
        });
      }

      $events = $query->get();

      return $events->map(function (Event $event) {
        return [
          'uuid' => $event->uuid,
          'name' => $event->name,
          'description' => $event->description,
          'location' => $event->location,
          'date' => $event->event_date?->toDateString(),
          'start' => $event->start_date_time?->toIso8601String(),
          'end' => $event->end_date_time?->toIso8601String(),
          'policies' => $event->policies->map(fn($policy) => [
            'hash' => $policy->hash,
            'name' => $policy->name,
          ])->values(),
          'policy_hashes' => $event->policies->pluck('hash')->values(),
        ];
      })->toArray();
    });


    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * Discover public events for a specific policy.
   *
   * Returns upcoming public events that accept the given Cardano policy ID.
   * This is a convenience wrapper around the main discover endpoint with a single policy filter.
   *
   * @group Public API
   * @unauthenticated
   *
   * @urlParam policyHash string required The Cardano policy ID to filter by.
   *                                     Example: a0028f35d4c7b4f2f0b1d6a0e4c3a9f6d0b0cafe
   *
   * @response 200 scenario="success" {
   *   "data": [
   *     {
   *       "uuid": "8f5fa03d-02c6-49f1-985f-d71544de8919",
   *       "name": "Hydra Launch Party",
   *       "description": "An exclusive Hydra-based launch event with live demos.",
   *       "location": "Las Vegas Convention Center",
   *       "date": "2025-01-24",
   *       "start": "2025-01-24T18:00:00Z",
   *       "end": "2025-01-24T21:00:00Z",
   *       "policies": [
   *         {
   *           "hash": "a0028f35d4c7b4f2f0b1d6a0e4c3a9f6d0b0cafe",
   *           "name": "HOSKY Token"
   *         }
   *       ],
   *       "policy_hashes": [
   *         "a0028f35d4c7b4f2f0b1d6a0e4c3a9f6d0b0cafe"
   *       ]
   *     }
   *   ]
   * }
   *
   * @responseField data[].uuid string The event UUID used in app links.
   * @responseField data[].name string The display name of the event.
   * @responseField data[].description string|null A short description of the event.
   * @responseField data[].location string|null The event venue or location.
   * @responseField data[].date string|null Event date in Y-m-d format.
   * @responseField data[].start string|null ISO8601 start datetime.
   * @responseField data[].end string|null ISO8601 end datetime.
   * @responseField data[].policies[].hash string Cardano policy ID accepted for this event.
   * @responseField data[].policies[].name string Human-readable label for the policy.
   * @responseField data[].policy_hashes string[] Convenience array of all policy hashes for this event.
   */
  public function byPolicy(Request $request, string $policyHash): JsonResponse
  {
    // Reuse the existing logic in index(), but pre-fill policy_hashes[]
    $request->merge([
      'policy_hashes' => [$policyHash],
    ]);

    return $this->index($request);
  }

  protected function buildCacheKey(array $policyHashes): string
  {
    if (empty($policyHashes)) {
      return 'public_events:all';
    }

    return 'public_events:policies:'.md5(json_encode($policyHashes));
  }

  /**
   * Display the specified event.
   */
  public function show(Request $request, string $eventUUID)
  {
    $event = Event::where('uuid', $eventUUID)
      ->with([
        'policies',
        'team'
      ])
      ->firstOrFail();

    return Jetstream::inertia()
      ->render($request, 'Event/Show', compact('event'));
  }

  /**
   * Connect your wallet and discover eligible events
   */
  public function discoverPage(Request $request)
  {
    // No props needed initially; the page will call the API itself
    return Jetstream::inertia()
      ->render($request, 'Event/Discover');
  }
}
