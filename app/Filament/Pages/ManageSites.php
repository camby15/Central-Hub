<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use UnitEnum;

class ManageSites extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sites';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'sites';

    protected string $view = 'filament.pages.manage-sites';

    #[Url]
    public string $search = '';

    public function getTitle(): string|Htmlable
    {
        return 'Sites';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Sites';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $sitesQuery = Site::query()
            ->withCount(['products', 'tickets'])
            ->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $sitesQuery->whereIn('id', $user->accessibleSiteIds());
        }

        $search = trim($this->search);

        if ($search !== '') {
            $sitesQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        return [
            'sites' => HubViewData::sites($sitesQuery->get()),
        ];
    }
}
