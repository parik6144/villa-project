<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CompanyMeta;
use App\Models\CompanyEmployee;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $companyMeta = $this->record->companyMeta;

        if ($companyMeta) {
            $data['companyMeta'] = $companyMeta->toArray();
        }

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $record->companyMeta()->updateOrCreate(
            ['user_id' => $record->id],
            $data['companyMeta'] 
        );
        return $record;
    }

    protected function beforeSave() {
    }
    protected function afterSave() {
        $companyId = $this->data['id'] ?? null;
        $employees = $this->data['employees'] ?? [];
        CompanyEmployee::saveCompanyEmployeeData($companyId, $employees);
    }
}
