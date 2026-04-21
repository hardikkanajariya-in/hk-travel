<?php

namespace App\Modules\Trains\Contracts;

use App\Modules\Trains\DTO\TrainOfferData;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Support\Collection;

interface TrainProvider
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @return Collection<int, TrainOfferData>
     */
    public function search(TrainSearchCriteria $criteria): Collection;
}
