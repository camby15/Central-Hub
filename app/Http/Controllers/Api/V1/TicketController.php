<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateTicket;
use App\Enums\TicketSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Site;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        $email = $request->string('email')->toString();

        abort_if($email === '', 422, 'The email query parameter is required.');

        $tickets = Ticket::query()
            ->where('site_id', $site->id)
            ->where('customer_email', $email)
            ->latest()
            ->get();

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request, CreateTicket $createTicket): JsonResponse
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        $validated = $request->validated();

        if (isset($validated['category'])) {
            $validated['meta'] = array_merge($validated['meta'] ?? [], [
                'category' => $validated['category'],
            ]);
            unset($validated['category']);
        }

        $validated['source'] = TicketSource::Api;

        $ticket = $createTicket->handle($site, $validated);

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $number): TicketResource
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        $ticket = Ticket::query()
            ->where('site_id', $site->id)
            ->where('number', $number)
            ->with(['replies' => fn ($query) => $query->where('is_internal', false)])
            ->firstOrFail();

        return new TicketResource($ticket);
    }
}
