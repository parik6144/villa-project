<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Concerns\HasActions;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasDescription;
use Filament\Support\Concerns\HasHeading;
use Filament\Forms\Form;
use Filament\Support\Contracts\HasLabel;

class LocationChangedModal extends Field
{
    use HasActions;
    use HasHeading;
    use HasDescription;

    protected string $view = 'components.modals.location-changed-modal';
}