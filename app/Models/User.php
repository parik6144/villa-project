<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static $isCreatingCompany = false;
    protected static function generateRandomPassword($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
    /**
     * Method for creating a company.
     */
    public static function createCompany(array $attributes)
    {
        static::$isCreatingCompany = true;
        $attributes['password'] = bcrypt(static::generateRandomPassword());
        $user = static::create($attributes);
        static::$isCreatingCompany = false;
        return $user;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->password)) {
                unset($model->password);
            }
        });

        static::saving(function ($user) {
            $userMeta = $user->userMeta;

            if ($userMeta) {
                $latestLog = Activity::where('subject_type', get_class($userMeta))
                    ->where('subject_id', $userMeta->user_id)
                    ->latest('created_at')
                    ->skip(1)
                    ->first();

                if ($latestLog) {
                    $latestLog->delete();
                }
            }
        });

        static::created(function ($user) {
            if (static::$isCreatingCompany) {
                $role = Role::firstOrCreate(['name' => 'company']);
                $user->assignRole($role);
            }
        });
    }




    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }



    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return str_ends_with($this->email, '@dits.md'); // && $this->hasVerifiedEmail();
    // }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name; // Склеиваем имя и фамилию
    }

    public function canAccessPanel(Panel $panel): bool
    {
        Log::debug($panel->getId());

        if ($panel->getId() === 'backend' && $this->hasRole('admin')) {
            return true;
        }

        if ($panel->getId() === 'backend' && $this->hasRole('agent')) {
            return true;
        }
        if ($panel->getId() === 'backend' && $this->hasRole('property_owner')) {
            return true;
        }
        if ($panel->getId() === 'backend' && $this->hasRole('manager')) {
            return true;
        }

        return false;
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
    public function userMeta()
    {
        return $this->hasOne(UserMeta::class, 'user_id', 'id');
    }
    public function getRouteKeyName()
    {
        return request()->is('*agents') ? 'user_id' : 'id';
    }

    public function getFilamentName(): string
    {
        return $this->getAttributeValue('name');
    }
    public function companyMeta()
    {
        return $this->hasOne(CompanyMeta::class, 'user_id');
    }
    public function companyEmployee()
    {
        return $this->hasOne(CompanyEmployee::class, 'employee_user_id');
    }
    public function employees()
    {
        return $this->hasMany(CompanyEmployee::class, 'company_user_id');
    }
    public function companies()
    {
        return $this->hasMany(CompanyEmployee::class, 'employee_user_id');
    }

    public function reservations()
    {
        return $this->hasManyThrough(
            Reservation::class,
            UserMeta::class,
            'user_id',         // Поле в `user_meta`, которое связывается с `users.id`
            'client_id',       // Поле в `reservations`, которое содержит Planyo ID
            'id',              // Поле в `users`, по которому связываем `user_meta`
            'user_planyo_id'   // Поле в `user_meta`, по которому связываем с `reservations`
        );
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
