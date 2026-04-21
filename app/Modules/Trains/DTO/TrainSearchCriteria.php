<?php

namespace App\Modules\Trains\DTO;

class TrainSearchCriteria
{
    public function __construct(
        public string $origin,
        public string $destination,
        public string $departDate,
        public ?string $returnDate = null,
        public int $adults = 1,
        public int $children = 0,
        public string $class = 'standard',
        public string $currency = 'USD',
    ) {}

    public function isRoundTrip(): bool
    {
        return ! empty($this->returnDate);
    }
}
