<?php

namespace App\Http\Controllers;

use App\Support\DemoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function show(string $token): View
    {
        $ticket = DemoData::ticket(1);
        $site = DemoData::site($ticket['site_id']);

        return view('status.show', [
            'token' => $token,
            'ticket' => $ticket,
            'replies' => collect(DemoData::replies())->where('is_internal', false)->values()->all(),
            'statuses' => DemoData::statusLabels(),
            'brandName' => $site['name'] ?? 'Support',
            'logoLabel' => $site['logo_label'] ?? 'CH',
            'primaryColor' => $site['primary_color'] ?? '#4338CA',
        ]);
    }

    public function reply(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        return back()->with('status', 'Your reply was sent. We will notify you when an agent responds.');
    }
}
