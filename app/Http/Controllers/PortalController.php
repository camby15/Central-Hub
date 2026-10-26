<?php

namespace App\Http\Controllers;

use App\Actions\CreateTicket;
use App\Enums\TicketSource;
use App\Models\Site;
use App\Models\Ticket;
use App\Support\HubViewData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function show(string $slug): View
    {
        $site = Site::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
            ->firstOrFail();

        $presented = HubViewData::site($site);

        return view('portal.show', [
            'site' => $presented,
            'brandName' => $presented['name'],
            'logoLabel' => $presented['logo_label'],
            'primaryColor' => $presented['primary_color'],
            'tab' => request()->string('tab', 'submit')->toString(),
        ]);
    }

    public function submit(Request $request, string $slug, CreateTicket $createTicket): RedirectResponse
    {
        $site = Site::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $ticket = $createTicket->handle($site, [
            ...$validated,
            'source' => TicketSource::Widget,
        ]);

        return redirect()
            ->route('status.show', ['token' => $ticket->customer_token])
            ->with('status', 'Ticket '.$ticket->number.' created. Track progress on this page.');
    }

    public function track(Request $request, string $slug): RedirectResponse
    {
        $site = Site::query()
            ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
            ->firstOrFail();

        $validated = $request->validate([
            'ticket_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $ticket = Ticket::query()
            ->where('site_id', $site->id)
            ->where('number', $validated['ticket_number'])
            ->where('customer_email', $validated['email'])
            ->firstOrFail();

        return redirect()->route('status.show', ['token' => $ticket->customer_token]);
    }
}
