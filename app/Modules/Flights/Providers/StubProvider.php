<?php

namespace App\Modules\Flights\Providers;

use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightOfferData;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use App\Modules\Flights\Models\FlightOffer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StubProvider implements FlightProvider
{
    public function name(): string
    {
        return 'stub';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function search(FlightSearchCriteria $criteria): Collection
    {
        $stored = FlightOffer::query()
            ->where('is_published', true)
            ->where('origin', $criteria->origin)
            ->where('destination', $criteria->destination)
            ->orderBy('price')
            ->get()
            ->map(fn (FlightOffer $o): FlightOfferData => new FlightOfferData(
                id: (string) $o->id,
                airline: (string) $o->airline,
                airlineCode: (string) $o->airline_code,
                flightNumber: (string) $o->flight_number,
                origin: (string) $o->origin,
                destination: (string) $o->destination,
                departTime: (string) $o->depart_time,
                arriveTime: (string) $o->arrive_time,
                durationMinutes: (int) $o->duration_minutes,
                stops: (int) $o->stops,
                price: (float) $o->price,
                currency: (string) $o->currency,
                cabin: (string) ($o->cabin ?? 'economy'),
                provider: 'stub',
            ));

        if ($stored->isNotEmpty()) {
            return $stored;
        }

        // Generate synthetic offers if nothing stored.
        return collect(range(1, 6))->map(function (int $i) use ($criteria): FlightOfferData {
            $airlines = [['Sky Airways', 'SA'], ['Globe Airlines', 'GA'], ['Cloud Jet', 'CJ'], ['Atlas Air', 'AT']];
            [$airline, $code] = $airlines[$i % count($airlines)];
            $depart = Carbon::parse($criteria->departDate)->setTime(6 + $i * 2, 30);
            $duration = 90 + $i * 35;

            return new FlightOfferData(
                id: 'stub-'.$i,
                airline: $airline,
                airlineCode: $code,
                flightNumber: $code.str_pad((string) (100 + $i), 3, '0', STR_PAD_LEFT),
                origin: strtoupper($criteria->origin),
                destination: strtoupper($criteria->destination),
                departTime: $depart->toIso8601String(),
                arriveTime: $depart->copy()->addMinutes($duration)->toIso8601String(),
                durationMinutes: $duration,
                stops: $i % 3 === 0 ? 1 : 0,
                price: 120 + $i * 47.5,
                currency: $criteria->currency,
                cabin: $criteria->cabin,
                provider: 'stub',
            );
        });
    }
}
