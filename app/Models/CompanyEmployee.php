<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyEmployee extends Model
{
    use HasFactory;

    protected $table = 'company_employees';

    protected $fillable = [
        'company_user_id',
        'employee_user_id',
        'role'
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_user_id')->where('role', 'company');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_user_id');
    }

    public static function saveCompanyEmployeeData(int $companyId, array $employees)
    {
        $existingEmployees = self::where('company_user_id', $companyId)->pluck('employee_user_id')->toArray();

        $newEmployeeIds = collect($employees)->pluck('employee_user_id')->toArray();

        $employeesToDelete = array_diff($existingEmployees, $newEmployeeIds);
        if (!empty($employeesToDelete)) {
            self::where('company_user_id', $companyId)
                ->whereIn('employee_user_id', $employeesToDelete)
                ->delete();
        }

        foreach ($employees as $employee) {
            self::updateOrCreate(
                [
                    'company_user_id' => $companyId,
                    'employee_user_id' => $employee['employee_user_id'],
                ],
                [
                    'role' => $employee['role'] ?? ($employee['role_label'] === 'Other' ? $employee['role_other'] : $employee['role_label']),
                ]
            );
        }
    }
}

?>