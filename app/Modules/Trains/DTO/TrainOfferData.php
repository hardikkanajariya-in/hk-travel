<?php

namespace App\Modules\Trains\DTO;

class TrainOfferData
{
    /**
     * @param  array<int, array<string, mixed>>  $segments
     */
    public function __construct(
        public string $id,
        public string $operator,
        public string $operatorCode,
        public string $trainNumber,
        public string $origin,
        public string $destination,
        public string $departTime,
        public string $arriveTime,
        public int $durationMinutes,
        public int $changes,
        public float $price,
        public string $currency,
        public string $class,
        public ?string $fareType = null,
        public ?bool $refundable = null,
        public array $segments = [],
        public string $provider = 'stub',
    ) {}
}
