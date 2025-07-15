<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class CustomTabs extends Tabs
{
    protected string $view = 'filament.components.custom-tabs';

    public function getTabsData()
    {
        return collect($this->getChildComponentContainer()->getComponents())
            ->filter(fn(Tab $tab) => $tab->isVisible())
            ->map(fn(Tab $tab) => $tab->getId())
            ->values()
            ->toJson();
    }
}
