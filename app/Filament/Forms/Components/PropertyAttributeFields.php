<?php

namespace App\Filament\Forms\Components;

use App\Models\Attribute;
use App\Models\Property;
use App\Models\AttributeGroup;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Filament\Resources\PropertyResource;
use Filament\Support\RawJs;

class PropertyAttributeFields
{
    public static function create(Property $property) : array
    {
        // Fetch all attribute groups with their associated attributes
        $attributeGroups = AttributeGroup::with('attributes')->get();
        $tabs  = [];

        // Iterate over each attribute group
        foreach ($attributeGroups as $group) {
            // Create a section for each attribute group
            $groupFields = [];

            foreach ($group->attributes as $attribute) {
                $field = match ($attribute->type) {
                    'select' => Select::make("property_attributes[{$attribute->id}]")
                        ->label($attribute->name)
                        ->options(json_decode($attribute->options, true))
                        ->formatStateUsing(fn(?Property $record) => $record?->property_attributes
                            ->firstWhere('attribute_id', $attribute->id)?->value ?? ''),
                    'text' => TextInput::make("property_attributes[{$attribute->id}]")
                        ->label($attribute->name)
                        ->formatStateUsing(fn(?Property $record) => $record?->property_attributes
                            ->firstWhere('attribute_id', $attribute->id)?->value ?? $attribute->default),
                    'textarea' => Textarea::make("property_attributes[{$attribute->id}]")
                        ->label($attribute->name)
                        ->formatStateUsing(fn(?Property $record) => $record?->property_attributes
                            ->firstWhere('attribute_id', $attribute->id)?->value ?? $attribute->default),
                    'checkbox' => Checkbox::make("property_attributes[{$attribute->id}]")
                        ->label($attribute->name)
                        ->formatStateUsing(fn(?Property $record) => $record?->property_attributes
                            ->firstWhere('attribute_id', $attribute->id)?->value ?? ''),
                    'number' => TextInput::make("property_attributes[{$attribute->id}]")
                        ->label($attribute->name)
                        ->numeric()
                        ->extraInputAttributes(['min' => '0'])
                        ->mask(RawJs::make(<<<'JS'
        $input.replace(/[^0-9]+/g, '')
    JS))
                        ->extraAttributes([
                            'inputmode' => 'numeric',
                            'pattern' => '/[^0-9]+/g',
                        ])
                        ->minValue(0)
                        ->formatStateUsing(fn(?Property $record) => $record?->property_attributes
                            ->firstWhere('attribute_id', $attribute->id)?->value ?? $attribute->default),
                    
                    'multi-checkbox' => CheckboxList::make("property_attributes[{$attribute->id}]")
                            ->label($attribute->name)
                            ->options(fn() => collect(json_decode($attribute->options, true))
                                ->mapWithKeys(fn($value, $key) => [$key => is_array($value) ? $value['label'] : $value])
                                ->toArray()
                            )
                            ->formatStateUsing(fn(?Property $record) => 
                                $record?->property_attributes->firstWhere('attribute_id', $attribute->id)?->value
                                    ? json_decode($record->property_attributes->firstWhere('attribute_id', $attribute->id)->value, true)
                                    : collect(json_decode($attribute->options, true))
                                        ->filter(fn($option) => is_array($option) && ($option['default'] ?? false))
                                        ->keys()
                                        ->toArray()
                            )
                            ->dehydrateStateUsing(fn($state) => json_encode(array_values($state))),
                    default => null,
                };

                $field
                    // ->live(true)
                    ->afterStateUpdated(function (Set $set, $old, $state, $livewire, $component) use ($group) { 
                        if ($old !== $state) {
                            PropertyResource::validateTabsAction($livewire, $group->name);
                            $livewire->validateOnly($component->getStatePath());
                        }
                    });

                // Check for description, notification, and example fields in the attribute model
                if ($field) {
                    if ($attribute->is_required) {
                        $field->required(); // Make the input field required
                    }

                    // Add additional information if available in the attribute model
                    if (!empty($attribute->description)) {
                        $field->helperText($attribute->description); // Add description as helper text
                    }

                    if (!empty($attribute->notification)) {
                        $field->hint($attribute->notification); // Add notification as a hint
                    }

                    if (!empty($attribute->example)) {
                        $field->placeholder($attribute->example); // Add example as a placeholder
                    }

                    $groupFields[] = $field;
                }
            }

            // Add the group of fields to the main fields array, wrapped in a section
            // if (!empty($groupFields)) {
            //     $fields[] = Section::make($group->name)->schema($groupFields);
            // }
            if (!empty($groupFields)) {
                $tabs[] = Tabs\Tab::make($group->name)
                            ->schema($groupFields)
                            ->icon(fn (Get $get) => $get('tab_icon_' . strtolower(str_replace(' ', '_', $group->name))))
                            ->icon(fn (Get $get) => PropertyResource::getTabIcon(  $get('tab_icon_' . strtolower(str_replace(' ', '_', $group->name))) ))
                            ->iconPosition(IconPosition::After);
            }
        }

        return $tabs;
    }
}
