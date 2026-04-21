<?php

namespace App\Modules\Trains\Providers;

use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainOfferData;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sabre Rail (GDS) train offers provider.
 *
 * Hits the Sabre Rail Shopping API once an OAuth2 token is in cache.
 *
 * @see https://developer.sabre.com/docs/rest_apis/rail
 */
class SabreRailProvider implements TrainProvider
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'sabre';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['client_id']) && ! empty($this->config['client_secret']);
    }

    public function search(TrainSearchCriteria $criteria): Collection
    {
        if (! $this->isConfigured()) {
            return collect();
        }

        try {
            $token = $this->accessToken();
            if (! $token) {
                return collect();
            }

            $base = (string) ($this->config['base_url'] ?? 'https://api.cert.platform.sabre.com');
            $response = Http::withToken($token)->timeout(20)->post("{$base}/v1/offers/shop/rail", [
                'OriginDestinationInformation' => [[
                    'DepartureDateTime' => $criteria->departDate.'T00:00:00',
                    'OriginLocation' => ['LocationCode' => strtoupper($criteria->origin)],
                    'DestinationLocation' => ['LocationCode' => strtoupper($criteria->destination)],
                ]],
                'TravelerInfoSummary' => [
                    'AirTravelerAvail' => [[
                        'PassengerTypeQuantity' => array_filter([
                            ['Code' => 'ADT', 'Quantity' => $criteria->adults],
                            $criteria->children > 0 ? ['Code' => 'CNN', 'Quantity' => $criteria->children] : null,
                        ]),
                    ]],
                ],
                'TPA_Extensions' => [
                    'CabinClass' => strtoupper($criteria->class),
                    'CurrencyCode' => $criteria->currency,
                ],
            ]);

            if ($response->failed()) {
                Log::warning('Sabre Rail search failed', ['status' => $response->status(), 'body' => $response->body()]);

                return collect();
            }

            return collect($response->json('OffersResponse.Offers', []))->map(function (array $offer) use ($criteria): TrainOfferData {
                $segments = $offer['Segments'] ?? [];
                $first = $segments[0] ?? [];
                $last = end($segments) ?: $first;

                return new TrainOfferData(
                    id: (string) ($offer['OfferId'] ?? ''),
                    operator: (string) ($first['Operator']['Name'] ?? ''),
                    operatorCode: (string) ($first['Operator']['Code'] ?? ''),
                    trainNumber: (string) ($first['TrainNumber'] ?? ''),
                    origin: (string) ($first['Origin']['LocationCode'] ?? $criteria->origin),
                    destination: (string) ($last['Destination']['LocationCode'] ?? $criteria->destination),
                    departTime: (string) ($first['DepartureDateTime'] ?? ''),
                    arriveTime: (string) ($last['ArrivalDateTime'] ?? ''),
                    durationMinutes: (int) ($offer['TotalDurationMinutes'] ?? 0),
                    changes: max(0, count($segments) - 1),
                    price: (float) ($offer['TotalPrice']['Amount'] ?? 0),
                    currency: (string) ($offer['TotalPrice']['CurrencyCode'] ?? $criteria->currency),
                    class: (string) ($offer['CabinClass'] ?? $criteria->class),
                    fareType: (string) ($offer['FareType'] ?? null),
                    refundable: (bool) ($offer['Refundable'] ?? false),
                    segments: $segments,
                    provider: 'sabre',
                );
            });
        } catch (\Throwable $e) {
            Log::error('Sabre Rail search error', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    protected function accessToken(): ?string
    {
        return Cache::remember('trains.sabre.token', now()->addMinutes(25), function (): ?string {
            $base = (string) ($this->config['base_url'] ?? 'https://api.cert.platform.sabre.com');
            $credentials = base64_encode(($this->config['client_id'] ?? '').':'.($this->config['client_secret'] ?? ''));
            $response = Http::asForm()
                ->withHeaders(['Authorization' => 'Basic '.$credentials])
                ->timeout(10)
                ->post("{$base}/v2/auth/token", ['grant_type' => 'client_credentials']);

            return $response->successful() ? (string) $response->json('access_token') : null;
        });
    }
}
