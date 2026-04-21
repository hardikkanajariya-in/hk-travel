<?php

namespace App\Modules\Flights\Contracts;

use App\Modules\Flights\DTO\FlightOfferData;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use Illuminate\Support\Collection;

interface FlightProvider
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @return Collection<int, FlightOfferData>
     */
    public function search(FlightSearchCriteria $criteria): Collection;
}
