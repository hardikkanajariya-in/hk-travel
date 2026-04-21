<?php

namespace App\Modules\Trains\Providers;

use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainOfferData;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Trainline Partner Solutions train offers provider.
 *
 * @see https://developer.thetrainline.com/
 */
class TrainlineProvider implements TrainProvider
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'trainline';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['api_key']);
    }

    public function search(TrainSearchCriteria $criteria): Collection
    {
        if (! $this->isConfigured()) {
            return collect();
        }

        try {
            $base = (string) ($this->config['base_url'] ?? 'https://partner-api.thetrainline.com');
            $response = Http::withHeaders([
                'X-API-Key' => (string) $this->config['api_key'],
                'Accept' => 'application/json',
            ])->timeout(20)->post("{$base}/v1/journey-search", [
                'origin' => strtoupper($criteria->origin),
                'destination' => strtoupper($criteria->destination),
                'outbound_date_time' => $criteria->departDate.'T07:00:00',
                'inbound_date_time' => $criteria->isRoundTrip() ? $criteria->returnDate.'T17:00:00' : null,
                'passengers' => array_merge(
                    array_fill(0, max(1, $criteria->adults), ['type' => 'adult']),
                    array_fill(0, $criteria->children, ['type' => 'child']),
                ),
                'cabin_class' => $criteria->class,
                'currency' => $criteria->currency,
            ]);

            if ($response->failed()) {
                Log::warning('Trainline search failed', ['status' => $response->status(), 'body' => $response->body()]);

                return collect();
            }

            return collect($response->json('outbound_journeys', []))->map(function (array $j) use ($criteria): TrainOfferData {
                $legs = $j['legs'] ?? [];
                $first = $legs[0] ?? [];
                $last = end($legs) ?: $first;
                $cheapest = collect($j['fares'] ?? [])->sortBy('price.amount')->first() ?? [];

                return new TrainOfferData(
                    id: (string) ($j['id'] ?? ''),
                    operator: (string) ($first['operator']['name'] ?? ''),
                    operatorCode: (string) ($first['operator']['code'] ?? ''),
                    trainNumber: (string) ($first['service_identifier'] ?? ''),
                    origin: (string) ($first['origin']['code'] ?? $criteria->origin),
                    destination: (string) ($last['destination']['code'] ?? $criteria->destination),
                    departTime: (string) ($first['departure_time'] ?? ''),
                    arriveTime: (string) ($last['arrival_time'] ?? ''),
                    durationMinutes: (int) ($j['duration_minutes'] ?? 0),
                    changes: max(0, count($legs) - 1),
                    price: (float) ($cheapest['price']['amount'] ?? 0),
                    currency: (string) ($cheapest['price']['currency'] ?? $criteria->currency),
                    class: (string) ($cheapest['cabin_class'] ?? $criteria->class),
                    fareType: (string) ($cheapest['fare_type'] ?? null),
                    refundable: (bool) ($cheapest['refundable'] ?? false),
                    segments: $legs,
                    provider: 'trainline',
                );
            });
        } catch (\Throwable $e) {
            Log::error('Trainline search error', ['error' => $e->getMessage()]);

            return collect();
        }
    }
}
