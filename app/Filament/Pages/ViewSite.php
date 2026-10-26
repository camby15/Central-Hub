<?php

namespace App\Filament\Pages;

use App\Actions\RotateSiteKey;
use App\Models\Site;
use App\Support\HubViewData;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use UnitEnum;

class ViewSite extends Page
{
    use AuthorizesRequests;

    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'sites/view';

    protected string $view = 'filament.pages.view-site';

    #[Url]
    public int $site = 0;

    public ?string $revealedKey = null;

    public function mount(): void
    {
        abort_unless($this->siteModel() !== null, 404);
        $this->authorize('view', $this->siteModel());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->siteModel()?->name ?? 'Site';
    }

    public function rotateKey(RotateSiteKey $rotateSiteKey): void
    {
        $model = $this->siteModel();
        abort_unless($model !== null, 404);
        $this->authorize('rotateKey', $model);

        $result = $rotateSiteKey->handle($model);
        $this->revealedKey = $result['plain_text'];

        Notification::make()
            ->title('API key rotated')
            ->body('Copy the new key now — it will not be shown again.')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $model = $this->siteModel();
        abort_unless($model !== null, 404);

        $site = HubViewData::site($model);

        return [
            'site' => $site,
            'revealedKey' => $this->revealedKey,
            'embed' => '<script src="'.url('/widget.js').'" data-site-key="pk_'.$site['api_key_prefix'].'" data-color="'.$site['primary_color'].'" defer></script>',
        ];
    }

    private function siteModel(): ?Site
    {
        if ($this->site <= 0) {
            return null;
        }

        return Site::query()->find($this->site);
    }
}
