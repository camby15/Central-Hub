<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Site;
use App\Support\HubViewData;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use UnitEnum;

class ManageProducts extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Sites';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'products';

    protected string $view = 'filament.pages.manage-products';

    public ?int $site_id = null;

    public string $name = '';

    public bool $is_active = true;

    public function getTitle(): string|Htmlable
    {
        return 'Products';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Products';
    }

    public function createProduct(): void
    {
        $user = Filament::auth()->user();

        $validated = $this->validate([
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['boolean'],
        ]);

        abort_unless($user?->canAccessSite((int) $validated['site_id']) ?? false, 403);

        $slug = Str::slug($validated['name']);
        $slug = $slug !== '' ? $slug : Str::random(8);

        $exists = Product::query()
            ->where('site_id', $validated['site_id'])
            ->where('slug', $slug)
            ->exists();

        if ($exists) {
            $this->addError('name', 'A product with this name already exists on that site.');

            return;
        }

        Product::query()->create([
            'site_id' => $validated['site_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $validated['is_active'],
        ]);

        $this->reset(['site_id', 'name']);
        $this->is_active = true;

        Notification::make()
            ->title('Product created')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();

        $productsQuery = Product::query()->with('site')->withCount('tickets')->orderBy('name');
        $sitesQuery = Site::query()->orderBy('name');

        if ($user !== null && ! $user->canAccessAllSites()) {
            $siteIds = $user->accessibleSiteIds();
            $productsQuery->whereIn('site_id', $siteIds);
            $sitesQuery->whereIn('id', $siteIds);
        }

        return [
            'products' => HubViewData::products($productsQuery->get()),
            'sites' => HubViewData::sites($sitesQuery->get()),
        ];
    }
}
