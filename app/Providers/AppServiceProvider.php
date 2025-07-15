<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Config;
use App\Models\S3Setting;
use App\Models\PropertySetting;
use Illuminate\Support\Facades\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Field::macro('tooltip', function (string $tooltip) {
            return $this->hintAction(
                Action::make('help')
                    ->icon('heroicon-o-question-mark-circle')
                    ->extraAttributes(["class" => "text-gray-500"])
                    ->tooltip($tooltip)
            );
        });

        if (Schema::hasTable('s3_settings')) {
            $s3Settings = S3Setting::first();

            if ($s3Settings) {
                Config::set('filesystems.disks.r2', [
                    'driver' => 's3',
                    'key' => $s3Settings->key,
                    'secret' => $s3Settings->secret,
                    'region' => $s3Settings->region ?: 'auto',
                    'bucket' => $s3Settings->bucket,
                    'endpoint' => $s3Settings->endpoint,
                    'use_path_style_endpoint' => $s3Settings->use_path_style_endpoint ?? false,
                    'visibility' => $s3Settings->visibility ?: 'public',
                    'url' => $s3Settings->url,
                    'throw' => $s3Settings->throw ?? false,
                ]);
            }
        }

        if (Schema::hasTable('property_settings')) {
            $settings = PropertySetting::first();

            if ($settings && $settings->google_map_api_key) {
                putenv('GOOGLE_MAPS_API_KEY=' . $settings->google_map_api_key);

                Config::set('google-maps.key', $settings->google_map_api_key);
                Config::set('filament-google-maps.keys.web_key', $settings->google_map_api_key);
                Config::set('filament-google-maps.keys.server_key', $settings->google_map_api_key);
            }
        }

        //Disable form on loading
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn (): View => view('filament.components.loader'),
        );
    }
}
