<?php

namespace App\Modules\Flights\Providers;

use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use Illuminate\Support\Collection;
use Throwable;

class FallbackFlightProvider implements FlightProvider
{
    public function __construct(
        protected FlightProvider $preferred,
        protected StubProvider $fallback,
    ) {}

    public function name(): string
    {
        return $this->preferred->isConfigured() ? $this->preferred->name() : $this->fallback->name();
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function search(FlightSearchCriteria $criteria): Collection
    {
        if (! $this->preferred->isConfigured()) {
            return $this->fallback->search($criteria);
        }

        try {
            return $this->preferred->search($criteria);
        } catch (Throwable) {
            return $this->fallback->search($criteria);
        }
    }
}
