<?php

namespace App\Modules\Flights\DTO;

class FlightOfferData
{
    /**
     * @param  array<int, array<string, mixed>>  $segments
     */
    public function __construct(
        public string $id,
        public string $airline,
        public string $airlineCode,
        public string $flightNumber,
        public string $origin,
        public string $destination,
        public string $departTime,
        public string $arriveTime,
        public int $durationMinutes,
        public int $stops,
        public float $price,
        public string $currency,
        public string $cabin,
        public array $segments = [],
        public string $provider = 'stub',
    ) {}
}
