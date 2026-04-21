<?php

namespace App\Modules\Flights\DTO;

class FlightSearchCriteria
{
    public function __construct(
        public string $origin,
        public string $destination,
        public string $departDate,
        public ?string $returnDate = null,
        public int $adults = 1,
        public int $children = 0,
        public int $infants = 0,
        public string $cabin = 'economy',
        public string $currency = 'USD',
    ) {}

    public function isRoundTrip(): bool
    {
        return ! empty($this->returnDate);
    }
}
