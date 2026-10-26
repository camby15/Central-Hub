<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->canAccessSite($ticket->site_id);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->canAccessSite($ticket->site_id);
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        return $user->canAccessSite($ticket->site_id);
    }
}
