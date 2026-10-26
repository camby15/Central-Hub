<?php

namespace App\Http\Controllers;

use App\Enums\AuthorType;
use App\Models\Ticket;
use App\Support\HubViewData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function show(string $token): View
    {
        $ticket = Ticket::query()
            ->where('customer_token', $token)
            ->with(['site', 'replies' => fn ($query) => $query->where('is_internal', false)->with('user')])
            ->firstOrFail();

        $site = $ticket->site;
        $presented = HubViewData::site($site);

        return view('status.show', [
            'token' => $token,
            'ticket' => HubViewData::ticket($ticket),
            'replies' => HubViewData::replies($ticket->replies),
            'statuses' => HubViewData::statusLabels(),
            'brandName' => $presented['name'],
            'logoLabel' => $presented['logo_label'],
            'primaryColor' => $presented['primary_color'],
        ]);
    }

    public function reply(Request $request, string $token): RedirectResponse
    {
        $ticket = Ticket::query()
            ->where('customer_token', $token)
            ->firstOrFail();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $ticket->replies()->create([
            'user_id' => null,
            'author_type' => AuthorType::Customer,
            'body' => $validated['body'],
            'is_internal' => false,
        ]);

        return back()->with('status', 'Your reply was sent. We will notify you when an agent responds.');
    }
}
