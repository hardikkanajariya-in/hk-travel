<?php

namespace App\Modules\Trains\Providers;

use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainOfferData;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use App\Modules\Trains\Models\TrainOffer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StubProvider implements TrainProvider
{
    public function name(): string
    {
        return 'stub';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function search(TrainSearchCriteria $criteria): Collection
    {
        $stored = TrainOffer::query()
            ->where('is_published', true)
            ->where('origin', $criteria->origin)
            ->where('destination', $criteria->destination)
            ->orderBy('price')
            ->get()
            ->map(fn (TrainOffer $o): TrainOfferData => new TrainOfferData(
                id: (string) $o->id,
                operator: (string) $o->operator,
                operatorCode: (string) $o->operator_code,
                trainNumber: (string) $o->train_number,
                origin: (string) $o->origin,
                destination: (string) $o->destination,
                departTime: (string) $o->depart_time,
                arriveTime: (string) $o->arrive_time,
                durationMinutes: (int) $o->duration_minutes,
                changes: (int) $o->changes,
                price: (float) $o->price,
                currency: (string) $o->currency,
                class: (string) ($o->class ?? 'standard'),
                fareType: $o->fare_type,
                refundable: (bool) $o->refundable,
                provider: 'stub',
            ));

        if ($stored->isNotEmpty()) {
            return $stored;
        }

        // Synthetic offers when no curated rows exist.
        $operators = [
            ['Eurostar', 'EST'], ['SNCF', 'SNF'], ['Trenitalia', 'TRN'],
            ['Renfe', 'RNF'], ['Deutsche Bahn', 'DB'], ['Amtrak', 'AMT'],
        ];

        return collect(range(1, 6))->map(function (int $i) use ($criteria, $operators): TrainOfferData {
            [$op, $code] = $operators[$i % count($operators)];
            $depart = Carbon::parse($criteria->departDate)->setTime(6 + $i * 2, 15);
            $duration = 60 + $i * 45;

            return new TrainOfferData(
                id: 'stub-'.$i,
                operator: $op,
                operatorCode: $code,
                trainNumber: $code.str_pad((string) (1000 + $i * 11), 4, '0', STR_PAD_LEFT),
                origin: strtoupper($criteria->origin),
                destination: strtoupper($criteria->destination),
                departTime: $depart->toIso8601String(),
                arriveTime: $depart->copy()->addMinutes($duration)->toIso8601String(),
                durationMinutes: $duration,
                changes: $i % 4 === 0 ? 1 : 0,
                price: 35 + $i * 18.5,
                currency: $criteria->currency,
                class: $criteria->class,
                fareType: $i % 2 === 0 ? 'flexible' : 'standard',
                refundable: $i % 2 === 0,
                provider: 'stub',
            );
        });
    }
}
