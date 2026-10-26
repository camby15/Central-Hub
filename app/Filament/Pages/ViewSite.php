<?php

namespace App\Filament\Pages;

use App\Support\DemoData;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use UnitEnum;

class ViewSite extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'sites/view';

    protected string $view = 'filament.pages.view-site';

    #[Url]
    public int $site = 1;

    public function getTitle(): string|Htmlable
    {
        return DemoData::site($this->site)['name'] ?? 'Site';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $site = DemoData::site($this->site) ?? DemoData::sites()[0];

        return [
            'site' => $site,
            'embed' => '<script src="'.url('/widget.js').'" data-site-key="pk_'.$site['api_key_prefix'].'_demo" data-color="'.$site['primary_color'].'" defer></script>',
        ];
    }
}
