<?php

namespace App\Filament\Pages;

use App\Enums\AuthorType;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use UnitEnum;

class ViewTicket extends Page
{
    use AuthorizesRequests;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Ticket detail';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'tickets/view';

    protected string $view = 'filament.pages.view-ticket';

    #[Url]
    public int $ticket = 0;

    public string $composerBody = '';

    public string $composerMode = 'reply';

    public function mount(): void
    {
        abort_unless($this->ticketModel() !== null, 404);
        $this->authorize('view', $this->ticketModel());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->ticketModel()?->number ?? 'Ticket';
    }

    public function getHeading(): string|Htmlable
    {
        $model = $this->ticketModel();

        if ($model === null) {
            return 'Ticket';
        }

        return $model->number.' · '.$model->subject;
    }

    public function sendComposer(): void
    {
        $model = $this->ticketModel();
        abort_unless($model !== null, 404);
        $this->authorize('reply', $model);

        $this->validate([
            'composerBody' => ['required', 'string', 'max:10000'],
            'composerMode' => ['required', 'in:reply,note'],
        ]);

        $user = Filament::auth()->user();

        $model->replies()->create([
            'user_id' => $user?->id,
            'author_type' => AuthorType::Agent,
            'body' => $this->composerBody,
            'is_internal' => $this->composerMode === 'note',
        ]);

        if ($model->first_responded_at === null && $this->composerMode === 'reply') {
            $model->forceFill(['first_responded_at' => now()])->save();
        }

        $this->composerBody = '';

        Notification::make()
            ->title($this->composerMode === 'note' ? 'Internal note saved' : 'Reply sent')
            ->success()
            ->send();
    }

    public function updateStatus(string $status): void
    {
        $model = $this->ticketModel();
        abort_unless($model !== null, 404);
        $this->authorize('update', $model);

        $model->forceFill([
            'status' => TicketStatus::from($status),
            'resolved_at' => $status === TicketStatus::Resolved->value ? now() : $model->resolved_at,
            'closed_at' => $status === TicketStatus::Closed->value ? now() : $model->closed_at,
        ])->save();
    }

    public function updatePriority(string $priority): void
    {
        $model = $this->ticketModel();
        abort_unless($model !== null, 404);
        $this->authorize('update', $model);

        $model->forceFill([
            'priority' => TicketPriority::from($priority),
        ])->save();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $model = $this->ticketModel();
        abort_unless($model !== null, 404);

        $model->load(['site', 'assignee', 'replies.user']);

        return [
            'ticket' => HubViewData::ticket($model),
            'replies' => HubViewData::replies($model->replies),
            'activity' => [
                ['event' => 'Ticket created', 'actor' => $model->source->value, 'at' => $model->created_at?->diffForHumans() ?? ''],
            ],
            'statuses' => HubViewData::statusLabels(),
            'priorities' => HubViewData::priorityLabels(),
        ];
    }

    private function ticketModel(): ?Ticket
    {
        if ($this->ticket <= 0) {
            return null;
        }

        return Ticket::query()->find($this->ticket);
    }
}
