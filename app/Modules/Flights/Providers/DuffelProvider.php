<?php

namespace App\Modules\Flights\Providers;

use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightOfferData;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Duffel flight offers provider.
 *
 * @see https://duffel.com/docs/api
 */
class DuffelProvider implements FlightProvider
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'duffel';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['access_token']);
    }

    public function search(FlightSearchCriteria $criteria): Collection
    {
        if (! $this->isConfigured()) {
            return collect();
        }

        try {
            $base = (string) ($this->config['base_url'] ?? 'https://api.duffel.com');
            $token = (string) $this->config['access_token'];

            $passengers = array_merge(
                array_fill(0, max(1, $criteria->adults), ['type' => 'adult']),
                array_fill(0, $criteria->children, ['type' => 'child']),
                array_fill(0, $criteria->infants, ['type' => 'infant_without_seat']),
            );

            $slices = [['origin' => $criteria->origin, 'destination' => $criteria->destination, 'departure_date' => $criteria->departDate]];
            if ($criteria->isRoundTrip()) {
                $slices[] = ['origin' => $criteria->destination, 'destination' => $criteria->origin, 'departure_date' => $criteria->returnDate];
            }

            $response = Http::withToken($token)
                ->withHeaders(['Duffel-Version' => 'v2', 'Accept' => 'application/json'])
                ->timeout(20)
                ->post("{$base}/air/offer_requests?return_offers=true", [
                    'data' => [
                        'slices' => $slices,
                        'passengers' => $passengers,
                        'cabin_class' => $criteria->cabin,
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('Duffel flight search failed', ['status' => $response->status(), 'body' => $response->body()]);

                return collect();
            }

            return collect($response->json('data.offers', []))->map(function (array $offer): FlightOfferData {
                $slice = $offer['slices'][0] ?? [];
                $segments = $slice['segments'] ?? [];
                $first = $segments[0] ?? [];
                $last = end($segments) ?: $first;

                return new FlightOfferData(
                    id: (string) ($offer['id'] ?? ''),
                    airline: (string) ($offer['owner']['name'] ?? ''),
                    airlineCode: (string) ($offer['owner']['iata_code'] ?? ''),
                    flightNumber: (string) (($first['marketing_carrier']['iata_code'] ?? '').($first['marketing_carrier_flight_number'] ?? '')),
                    origin: (string) ($first['origin']['iata_code'] ?? ''),
                    destination: (string) ($last['destination']['iata_code'] ?? ''),
                    departTime: (string) ($first['departing_at'] ?? ''),
                    arriveTime: (string) ($last['arriving_at'] ?? ''),
                    durationMinutes: $this->parseDuration((string) ($slice['duration'] ?? 'PT0M')),
                    stops: max(0, count($segments) - 1),
                    price: (float) ($offer['total_amount'] ?? 0),
                    currency: (string) ($offer['total_currency'] ?? 'USD'),
                    cabin: (string) ($first['passengers'][0]['cabin_class'] ?? 'economy'),
                    segments: $segments,
                    provider: 'duffel',
                );
            });
        } catch (\Throwable $e) {
            Log::error('Duffel flight search error', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    protected function parseDuration(string $iso): int
    {
        if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $iso, $m)) {
            return ((int) ($m[1] ?? 0)) * 60 + ((int) ($m[2] ?? 0));
        }

        return 0;
    }
}
