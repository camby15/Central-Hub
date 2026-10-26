<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AuthorType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Site;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class ReplyController extends Controller
{
    public function store(StoreReplyRequest $request, string $number): JsonResponse
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        $ticket = Ticket::query()
            ->where('site_id', $site->id)
            ->where('number', $number)
            ->firstOrFail();

        $reply = $ticket->replies()->create([
            'user_id' => null,
            'author_type' => AuthorType::Customer,
            'body' => $request->validated('body'),
            'is_internal' => false,
        ]);

        $site->touchLastSeen();

        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(201);
    }
}
