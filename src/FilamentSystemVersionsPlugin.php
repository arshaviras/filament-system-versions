<?php

namespace Cmsmaxinc\FilamentSystemVersions;

use Closure;
use Cmsmaxinc\FilamentSystemVersions\Filament\Pages\SystemVersions;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use UnitEnum;

class FilamentSystemVersionsPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool | Closure $authorizeUsing = true;

    protected ?string $navigationGroup = null;

    protected string | \BackedEnum | null $navigationIcon = null;

    protected ?string $navigationLabel = null;

    protected ?int $navigationSort = null;

    protected array $statsPackages = [];

    public function getId(): string
    {
        return 'filament-system-versions';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            SystemVersions::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Use reflection to set protected static properties
        $reflection = new \ReflectionClass(SystemVersions::class);

        $navigationGroup = $reflection->getProperty('navigationGroup');
        $navigationGroup->setAccessible(true);
        $navigationGroup->setValue(null, $this->getNavigationGroup());

        $navigationIcon = $reflection->getProperty('navigationIcon');
        $navigationIcon->setAccessible(true);
        $navigationIcon->setValue(null, $this->getNavigationIcon());

        $navigationLabel = $reflection->getProperty('navigationLabel');
        $navigationLabel->setAccessible(true);
        $navigationLabel->setValue(null, $this->getNavigationLabel());

        $navigationSort = $reflection->getProperty('navigationSort');
        $navigationSort->setAccessible(true);
        $navigationSort->setValue(null, $this->getNavigationSort());
    }

    public function authorize(bool | Closure $callback = true): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorizeUsing) === true;
    }

    public function navigationGroup(string | UnitEnum | null $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string | UnitEnum | null
    {
        return $this->navigationGroup ?? 'Settings';
    }

    public function navigationIcon(string | \BackedEnum | null $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): string | \BackedEnum | null
    {
        return $this->navigationIcon ?? 'heroicon-o-document-text';
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel ?? 'System Versions';
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? 99999;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function statsPackages(array $packages): static
    {
        $this->statsPackages = $packages;

        return $this;
    }

    public function getStatsPackages(): array
    {
        // Default packages if none specified
        if (empty($this->statsPackages)) {
            return [
                ['label' => 'Laravel', 'package' => 'laravel/framework'],
                ['label' => 'FilamentPHP', 'package' => 'filament/filament'],
            ];
        }

        return $this->statsPackages;
    }
}
