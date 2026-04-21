<?php

namespace App\Core\Crm;

use App\Core\Settings\SettingsRepository;
use App\Models\Lead;
use App\Models\User;

class LeadAssigner
{
    public function __construct(protected SettingsRepository $settings) {}

    public function assign(Lead $lead): ?User
    {
        if ($lead->assigned_to) {
            return $lead->assignee;
        }

        $strategy = (string) $this->settings->get('crm.assignment.strategy', 'none');
        $userIds = (array) $this->settings->get('crm.assignment.user_ids', []);

        if ($strategy === 'none' || empty($userIds)) {
            return null;
        }

        $user = match ($strategy) {
            'round_robin' => $this->roundRobin($userIds),
            'least_busy' => $this->leastBusy($userIds),
            default => null,
        };

        if ($user) {
            $lead->assigned_to = $user->id;
            $lead->save();
            $lead->logActivity('assignment', "Assigned to {$user->name}");
        }

        return $user;
    }

    /** @param array<int> $userIds */
    protected function roundRobin(array $userIds): ?User
    {
        $cursor = (int) $this->settings->get('crm.assignment.cursor', 0);
        $next = $userIds[$cursor % count($userIds)];
        $this->settings->set('crm.assignment.cursor', $cursor + 1);

        return User::find($next);
    }

    /** @param array<int> $userIds */
    protected function leastBusy(array $userIds): ?User
    {
        $counts = Lead::query()
            ->whereIn('assigned_to', $userIds)
            ->where('status', 'open')
            ->selectRaw('assigned_to, COUNT(*) as c')
            ->groupBy('assigned_to')
            ->pluck('c', 'assigned_to');

        $best = null;
        $min = PHP_INT_MAX;
        foreach ($userIds as $id) {
            $c = (int) ($counts[$id] ?? 0);
            if ($c < $min) {
                $min = $c;
                $best = $id;
            }
        }

        return $best ? User::find($best) : null;
    }
}
