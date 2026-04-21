<?php

namespace App\Modules\Flights\Providers;

use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightOfferData;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Amadeus Self-Service flight offers provider.
 *
 * @see https://developers.amadeus.com/self-service/category/flights
 */
class AmadeusProvider implements FlightProvider
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'amadeus';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['api_key']) && ! empty($this->config['api_secret']);
    }

    public function search(FlightSearchCriteria $criteria): Collection
    {
        if (! $this->isConfigured()) {
            return collect();
        }

        try {
            $token = $this->accessToken();
            if (! $token) {
                return collect();
            }

            $base = (string) ($this->config['base_url'] ?? 'https://test.api.amadeus.com');
            $response = Http::withToken($token)->timeout(15)->get("{$base}/v2/shopping/flight-offers", array_filter([
                'originLocationCode' => $criteria->origin,
                'destinationLocationCode' => $criteria->destination,
                'departureDate' => $criteria->departDate,
                'returnDate' => $criteria->returnDate,
                'adults' => $criteria->adults,
                'children' => $criteria->children > 0 ? $criteria->children : null,
                'infants' => $criteria->infants > 0 ? $criteria->infants : null,
                'travelClass' => strtoupper($criteria->cabin),
                'currencyCode' => $criteria->currency,
                'max' => 20,
            ]));

            if ($response->failed()) {
                Log::warning('Amadeus flight search failed', ['status' => $response->status(), 'body' => $response->body()]);

                return collect();
            }

            return collect($response->json('data', []))->map(function (array $offer): FlightOfferData {
                $itinerary = $offer['itineraries'][0] ?? [];
                $segments = $itinerary['segments'] ?? [];
                $first = $segments[0] ?? [];
                $last = end($segments) ?: $first;
                $segments = $segments ?: [];

                return new FlightOfferData(
                    id: (string) ($offer['id'] ?? ''),
                    airline: (string) ($first['carrierCode'] ?? ''),
                    airlineCode: (string) ($first['carrierCode'] ?? ''),
                    flightNumber: ($first['carrierCode'] ?? '').($first['number'] ?? ''),
                    origin: (string) ($first['departure']['iataCode'] ?? ''),
                    destination: (string) ($last['arrival']['iataCode'] ?? ''),
                    departTime: (string) ($first['departure']['at'] ?? ''),
                    arriveTime: (string) ($last['arrival']['at'] ?? ''),
                    durationMinutes: $this->parseDuration((string) ($itinerary['duration'] ?? 'PT0M')),
                    stops: max(0, count($segments) - 1),
                    price: (float) ($offer['price']['grandTotal'] ?? 0),
                    currency: (string) ($offer['price']['currency'] ?? 'USD'),
                    cabin: (string) ($offer['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] ?? 'ECONOMY'),
                    segments: $segments,
                    provider: 'amadeus',
                );
            });
        } catch (\Throwable $e) {
            Log::error('Amadeus flight search error', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    protected function accessToken(): ?string
    {
        return Cache::remember('flights.amadeus.token', now()->addMinutes(25), function (): ?string {
            $base = (string) ($this->config['base_url'] ?? 'https://test.api.amadeus.com');
            $response = Http::asForm()->timeout(10)->post("{$base}/v1/security/oauth2/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->config['api_key'] ?? '',
                'client_secret' => $this->config['api_secret'] ?? '',
            ]);

            return $response->successful() ? (string) $response->json('access_token') : null;
        });
    }

    protected function parseDuration(string $iso): int
    {
        if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $iso, $m)) {
            return ((int) ($m[1] ?? 0)) * 60 + ((int) ($m[2] ?? 0));
        }

        return 0;
    }
}
