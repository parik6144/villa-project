<?php

namespace App\Filament\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Filament\Resources\ExtendedRoleResource\Pages\ListExtendedRoles;
use App\Filament\Resources\ExtendedRoleResource\Pages\EditExtendedRole;
use App\Filament\Resources\ExtendedRoleResource\Pages\CreateExtendedRole;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class ExtendedRoleResource extends RoleResource
{

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExtendedRoles::route('/'),
            'create' => CreateExtendedRole::route('/create'),
            'edit' => EditExtendedRole::route('/{record}/edit'),
        ];
    }
}
