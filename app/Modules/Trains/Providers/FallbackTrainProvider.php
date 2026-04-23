<?php

namespace App\Modules\Trains\Providers;

use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Support\Collection;
use Throwable;

class FallbackTrainProvider implements TrainProvider
{
    public function __construct(
        protected TrainProvider $preferred,
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

    public function search(TrainSearchCriteria $criteria): Collection
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
