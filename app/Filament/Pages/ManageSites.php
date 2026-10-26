<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class ManageSites extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Sites';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'sites';

    protected string $view = 'filament.pages.manage-sites';

    public function getTitle(): string|Htmlable
    {
        return 'Sites';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $sitesQuery = Site::query()->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $sitesQuery->whereIn('id', $user->accessibleSiteIds());
        }

        return [
            'sites' => HubViewData::sites($sitesQuery->get()),
        ];
    }
}
