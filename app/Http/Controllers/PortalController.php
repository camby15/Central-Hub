<?php

namespace App\Http\Controllers;

use App\Support\DemoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function show(string $slug): View
    {
        $site = collect(DemoData::sites())->first(
            fn (array $item): bool => strtolower($item['slug']) === strtolower($slug),
            DemoData::sites()[0],
        );

        return view('portal.show', [
            'site' => $site,
            'brandName' => $site['name'],
            'logoLabel' => $site['logo_label'],
            'primaryColor' => $site['primary_color'],
            'tab' => request()->string('tab', 'submit')->toString(),
        ]);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        return redirect()
            ->route('status.show', ['token' => 'demo-status-token'])
            ->with('status', 'Ticket FRG-00043 created. Track progress on this page.');
    }

    public function track(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'ticket_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        return redirect()->route('status.show', ['token' => 'demo-status-token']);
    }
}
