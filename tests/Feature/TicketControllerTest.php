<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Policy;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
  use RefreshDatabase;

  static string $POLICY_ID = '40fa2aa67258b4ce7b5782f74831d46a84c59a0ff0c28262fab21728';
  static string $STAKE_KEY = 'stake1uxekfkqgs4ye2wnf38e7x8uy006wvleq3etu8t35gqmdmnq5v4rks';
  static string $ASSET_ID = '436c61794e6174696f6e35393835';

  /** @test */
  public function can_create_ticket_challenge_during_ticketing_window()
  {
    $event = Event::factory([
      'start_date_time' => now()->subDay(1),
      'end_date_time' => now()->addDay(1),
    ])->create();

    $event->policies()->save(Policy::factory(['hash' => self::$POLICY_ID])->create());

    $response = $this->post(route('ticket.store'), [
      'event_uuid' => $event->uuid,
      'policy_id' => self::$POLICY_ID,
      'asset_id' => self::$ASSET_ID,
      'stake_key' => self::$STAKE_KEY,
    ]);


    $response->assertOk();
    $response->assertJsonStructure(['id', 'nonce']);
  }

  /** @test */
  public function cannot_create_ticket_challenge_before_ticketing_window()
  {
    $event = Event::factory([
      'start_date_time' => now()->addDay(2),
      'end_date_time' => now()->addDay(3),
    ])->create();

    $event->policies()->save(Policy::factory(['hash' => self::$POLICY_ID])->create());

    $response = $this->post(route('ticket.store'), [
      'event_uuid' => $event->uuid,
      'policy_id' => self::$POLICY_ID,
      'asset_id' => bin2hex(random_bytes(16)),
      'stake_key' => self::$STAKE_KEY,
    ]);

    $response->assertStatus(409);
    $response->assertJson(["message" => "Sorry, ticketing is closed for this event."]);
  }

  /** @test */
  public function cannot_create_ticket_challenge_after_ticketing_window()
  {
    $event = Event::factory([
      'start_date_time' => now()->subDay(3),
      'end_date_time' => now()->subDay(1),
    ])->create();

    $event->policies()->save(Policy::factory(['hash' => self::$POLICY_ID])->create());

    $response = $this->post(route('ticket.store'), [
      'event_uuid' => $event->uuid,
      'policy_id' => self::$POLICY_ID,
      'asset_id' => bin2hex(random_bytes(16)),
      'stake_key' => self::$STAKE_KEY,
    ]);

    $response->assertStatus(409);
    $response->assertJson(["message" => "Sorry, ticketing is closed for this event."]);
  }

  /** @test */
  public function validate_signature_and_return_qr_value()
  {
    $event = Event::factory([
      'name' => 'Cardano Community Meetup',
      'uuid' => '36e1e81e-fe27-4967-8288-46eaed7da7b0',
      'start_date_time' => now()->subDay(1),
      'end_date_time' => now()->addDay(1),
    ])->create();

    $policy = Policy::factory(['hash' => self::$POLICY_ID])->create();

    $event->policies()->save($policy);

    $ticket = Ticket::factory([
      'asset_id' => self::$ASSET_ID,
      'stake_key' => self::$STAKE_KEY,
      'signature_nonce' => Uuid::fromString('e19801fb-649f-41e5-b509-469c63935582')->getBytes(),
      'created_at' => '2025-11-18 22:07:42',
      'updated_at' => '2025-11-18 22:07:42'
    ])->for($event)->for($policy)->create();

    $response = $this->put(route('ticket.update', [Uuid::fromBytes($ticket->signature_nonce)->toString()]), [
      'event_uuid' => '36e1e81e-fe27-4967-8288-46eaed7da7b0',
      'policy_id' => '40fa2aa67258b4ce7b5782f74831d46a84c59a0ff0c28262fab21728',
      'asset_id' => '436c61794e6174696f6e35393835',
      'stake_key' => 'stake1uxekfkqgs4ye2wnf38e7x8uy006wvleq3etu8t35gqmdmnq5v4rks',
      'nonce' => '7b2261737365744964223a2234333663363137393465363137343639366636653335333933383335222c226576656e744964223a2233366531653831652d666532372d343936372d383238382d343665616564376461376230222c226576656e744e616d65223a2243617264616e6f20436f6d6d756e697479204d6565747570222c22706f6c6963794964223a223430666132616136373235386234636537623537383266373438333164343661383463353961306666306332383236326661623231373238222c227369676e4279223a22323032352d31312d31385432323a32323a34322b30303a3030222c227374616b654b6579223a227374616b65317578656b666b71677334796532776e66333865377838757930303677766c6571336574753874333567716d646d6e71357634726b73222c227469636b65744964223a2265313938303166622d363439662d343165352d623530392d343639633633393335353832222c2274797065223a22476174654b65657065725469636b6574222c2276657273696f6e223a22312e302e30227d',
      'signature' => [
        'key' => 'a401010327200621582073b46827a5032a0d31f12aa6e58dd4b5dc9b98e77ebd1cee852f31598c65cfca',
        'signature' => '84582aa201276761646472657373581de1b364d8088549953a6989f3e31f847bf4e67f208e57c3ae344036ddcca166686173686564f45901947b2261737365744964223a2234333663363137393465363137343639366636653335333933383335222c226576656e744964223a2233366531653831652d666532372d343936372d383238382d343665616564376461376230222c226576656e744e616d65223a2243617264616e6f20436f6d6d756e697479204d6565747570222c22706f6c6963794964223a223430666132616136373235386234636537623537383266373438333164343661383463353961306666306332383236326661623231373238222c227369676e4279223a22323032352d31312d31385432323a32323a34322b30303a3030222c227374616b654b6579223a227374616b65317578656b666b71677334796532776e66333865377838757930303677766c6571336574753874333567716d646d6e71357634726b73222c227469636b65744964223a2265313938303166622d363439662d343165352d623530392d343639633633393335353832222c2274797065223a22476174654b65657065725469636b6574222c2276657273696f6e223a22312e302e30227d5840311d6519a24127b9dbdf9b2c8f7e097f79231d3711c55f32ed73ab2b647a7f30aff63a4f59922943eb01669cf3b3d042ddd768c90c66f3e8b4f308c643a4ec0b'
      ]
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['qr_value', 'security_code']);
  }
}
