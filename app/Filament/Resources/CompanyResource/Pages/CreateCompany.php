<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use App\Models\User;
use App\Models\CompanyMeta;
use App\Models\CompanyEmployee;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function handleRecordCreation(array $data): User
    {
        $user = User::createCompany($data);

        $companyMeta = new CompanyMeta([
            'user_id' => $user->id,
            'type' => $data['companyMeta']['type'],
            'about' => $data['companyMeta']['about'],
            'phone' => $data['companyMeta']['phone'],
            'country' => $data['companyMeta']['country'],
            'city' => $data['companyMeta']['city'],
            'address' => $data['companyMeta']['address'],
            'address2' => $data['companyMeta']['address2'],
            'state' => $data['companyMeta']['state'],
            'postal_code' => $data['companyMeta']['postal_code'],
            'website' => $data['companyMeta']['website'],
            'tax_id' => $data['companyMeta']['tax_id'],
            'iban' => $data['companyMeta']['iban'],
            'beneficiary' => $data['companyMeta']['beneficiary'],
        ]);

        $companyMeta->save();

        CompanyEmployee::saveCompanyEmployeeData($user->id, $data['employees']);
        
         return $user;
    }

}
