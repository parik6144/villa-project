<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\ReservationStatus;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\EditAction;
use Filament\Actions\StaticAction;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Секция: Основная информация по бронированию
            Section::make('Reservation Details')
                ->schema([
                    TextInput::make('planyo_reservation_id')
                        ->label(false)
                        ->prefix('Reservation ID')
                        ->readonly(),

                    Placeholder::make('name')
                        ->label(false)
                        ->content(fn($record) => 'Property - ' . $record->name ?? '-'),
                    Placeholder::make('duration')
                        ->label(false)
                        ->content(
                            fn($record) =>
                            "Duration - {$record->start_time->format('d.m.Y')} - {$record->end_time->format('d.m.Y')} (" .
                                $record->start_time->diffInDays($record->end_time) . " nights)"
                        ),
                    Placeholder::make('creation_time')
                        ->label(false)
                        ->content(fn($record) => 'Created - ' . $record->creation_time ?? '-'),
                    Placeholder::make('status')
                        ->label(false)
                        ->content(function ($state) {
                            if (empty($state)) {
                                return '-';
                            }

                            $statusMapping = [
                                ReservationStatus::COMPLETED          => 'Reservation completed',
                                ReservationStatus::EMAIL_VERIFIED     => 'Email verified',
                                ReservationStatus::CONFIRMED          => 'Reservation confirmed',
                                ReservationStatus::CANCELLED_BY_ADMIN => 'Cancelled by administrator',
                                ReservationStatus::CANCELLED_BY_USER  => 'Cancelled by user',
                                ReservationStatus::FRAUDULENT         => 'Fraudulent reservation',
                                ReservationStatus::CONFLICT           => 'Conflict',
                                ReservationStatus::UNAVAILABLE        => 'Does not affect availability',
                                ReservationStatus::CANCELLED_AUTO     => 'Cancelled automatically',
                                ReservationStatus::QUOTATION          => 'Quotation',
                                ReservationStatus::WAITING_LIST       => 'Waiting list request',
                                ReservationStatus::LOCKED             => 'Locked for modifications',
                                ReservationStatus::CHECKED_IN         => 'Client checked in',
                                ReservationStatus::CHECKED_OUT        => 'Client checked out',
                                ReservationStatus::NO_SHOW            => 'No-show',
                            ];

                            // Проверяем, является ли $state числом (битовая маска) или уже массивом
                            if (!is_array($state)) {
                                $stateArray = [];
                                foreach (array_keys($statusMapping) as $flag) {
                                    if (($state & $flag) === $flag) {
                                        $stateArray[] = $flag;
                                    }
                                }
                            } else {
                                $stateArray = $state;
                            }

                            // Преобразуем массив ключей в текстовые статусы
                            $selectedStatuses = array_map(fn($key) => $statusMapping[$key] ?? null, $stateArray);

                            // Убираем `null` значения
                            $selectedStatuses = array_filter($selectedStatuses);
                            if (empty($selectedStatuses)) {
                                return '-';
                            }

                            // Создаём HTML
                            $html = 'Status - ';
                            $index = 1;
                            $total = count($selectedStatuses);

                            foreach ($selectedStatuses as $status) {
                                if ($index === $total) {
                                    // Последний элемент - жирный и зелёный
                                    $html .= "<strong style='color: #0c0;'>{$index}. {$status}</strong> ";
                                } else {
                                    $html .= "{$index}. {$status} ";
                                }
                                $index++;
                            }

                            return new \Illuminate\Support\HtmlString($html);
                        }),

                ]),
            Section::make('Price & Payments')
                ->schema([
                    Placeholder::make('total_price')
                        ->label(false)
                        ->content(fn($record) => 'Price for owner - ' . $record->total_price . ' €' ?? '-'),
                    Placeholder::make('original_price')
                        ->label(false)
                        ->content(fn($record) => 'Price for agent - ' . $record->original_price . ' € (Discount: ' . $record->discount . ')' ?? '-')
                        ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'agent'])),

                    Placeholder::make('amount_paid')
                        ->label(false)
                        ->content(fn($record) => 'Payments - ' . $record->amount_paid . ' €' ?? '-')
                        ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'agent'])),


                    Placeholder::make('payments_list')
                        ->label(false)
                        ->content(function ($record) {
                            $payments = $record->payments;

                            if ($payments->isEmpty()) {
                                return 'No payments found.';
                            }

                            $html = '<ul style="padding-left: 20px;">';
                            foreach ($payments as $payment) {
                                $html .= "<li>";
                                $html .= "<strong>{$payment->payment_time->format('d.m.Y H:i')}</strong>: ";
                                $html .= "{$payment->amount} {$payment->currency}";
                                if ($payment->comment) {
                                    $html .= ", Status: {$payment->comment} ";
                                }
                                if ($payment->transaction_id) {
                                    $html .= ", Transaction ID: {$payment->transaction_id}";
                                }
                                $html .= "</li>";
                            }
                            $html .= '</ul>';

                            return new HtmlString($html);
                        })
                        ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'agent'])),

                ]),

            Section::make('Guests')
                ->schema([
                    Placeholder::make('adults')
                        ->label(false)
                        ->content(
                            fn($state, $record) => 'Number of adults - ' . (
                                is_array($record->properties)
                                ? ($record->properties['adults'] ?? '0')
                                : optional(json_decode($record->properties, true))['adults'] ?? '0'
                            )
                        ),
                    Placeholder::make('children_3_12')
                        ->label(false)
                        ->content(
                            fn($state, $record) => 'Children age 3-12 year - ' . (
                                is_array($record->properties)
                                ? ($record->properties['children_age_3_12_year'] ?? '0')
                                : optional(json_decode($record->properties, true))['children_age_3_12_year'] ?? '0'
                            )
                        ),
                    Placeholder::make('children_0_3')
                        ->label(false)
                        ->content(
                            fn($state, $record) => 'Children age before 3 year - ' . (
                                is_array($record->properties)
                                ? ($record->properties['children_age_0___8211_year'] ?? '0')
                                : optional(json_decode($record->properties, true))['children_age_0___8211_year'] ?? '0'
                            )
                        ),

                    Placeholder::make('user_notes')
                        ->label(false)
                        ->content(fn($record) => 'Notes - ' . ($record->user_notes ?? '—')),
                ]),

            Section::make('Client Information')
                ->schema([
                    Placeholder::make('first_name')
                        ->label(false)
                        ->content(fn($record) => 'First Name - ' . ($record->first_name ?? '—')),
                    Placeholder::make('last_name')
                        ->label(false)
                        ->content(fn($record) => 'Last Name - ' . ($record->last_name ?? '—')),
                    Placeholder::make('email')
                        ->label(false)
                        ->content(fn($record) => 'Email - ' . ($record->email ?? '—')),
                    Placeholder::make('status')
                        ->label(false)
                        ->content(function ($state) {
                            if (empty($state)) {
                                return '-';
                            }

                            $statusMapping = [
                                ReservationStatus::COMPLETED          => 'Reservation completed',
                                ReservationStatus::EMAIL_VERIFIED     => 'Email verified',
                                ReservationStatus::CONFIRMED          => 'Reservation confirmed',
                                ReservationStatus::CANCELLED_BY_ADMIN => 'Cancelled by administrator',
                                ReservationStatus::CANCELLED_BY_USER  => 'Cancelled by user',
                                ReservationStatus::FRAUDULENT         => 'Fraudulent reservation',
                                ReservationStatus::CONFLICT           => 'Conflict',
                                ReservationStatus::UNAVAILABLE        => 'Does not affect availability',
                                ReservationStatus::CANCELLED_AUTO     => 'Cancelled automatically',
                                ReservationStatus::QUOTATION          => 'Quotation',
                                ReservationStatus::WAITING_LIST       => 'Waiting list request',
                                ReservationStatus::LOCKED             => 'Locked for modifications',
                                ReservationStatus::CHECKED_IN         => 'Client checked in',
                                ReservationStatus::CHECKED_OUT        => 'Client checked out',
                                ReservationStatus::NO_SHOW            => 'No-show',
                            ];

                            // Проверяем, является ли $state массивом (список статусов) или битовой маской (число)
                            if (!is_array($state)) {
                                $stateArray = [];
                                foreach (array_keys($statusMapping) as $flag) {
                                    if (($state & $flag) === $flag) {
                                        $stateArray[] = $flag;
                                    }
                                }
                            } else {
                                $stateArray = $state;
                            }

                            // Преобразуем массив ключей в текстовые статусы
                            $selectedStatuses = array_map(fn($key) => $statusMapping[$key] ?? null, $stateArray);
                            $selectedStatuses = array_filter($selectedStatuses);

                            // Проверка наличия статуса EMAIL_VERIFIED
                            $isVerified = in_array(ReservationStatus::EMAIL_VERIFIED, $stateArray, true);

                            // Создаём HTML
                            $html = '';

                            // Добавляем итоговую строку
                            $html .= "Email verified – <strong style='color: " . ($isVerified ? '#0c0' : 'red') . ";'>" . ($isVerified ? 'yes' : 'no') . "</strong>";

                            return new \Illuminate\Support\HtmlString($html);
                        }),




                    Placeholder::make('address')
                        ->label(false)
                        ->content(fn($record) => 'Address - ' . ($record->address ?? '—')),
                    Placeholder::make('city')
                        ->label(false)
                        ->content(fn($record) => 'City - ' . ($record->city ?? '—')),
                    Placeholder::make('zip')
                        ->label(false)
                        ->content(fn($record) => 'Postal Code - ' . ($record->zip ?? '—')),
                    Placeholder::make('country')
                        ->label(false)
                        ->content(fn($record) => 'Country - ' . ($record->country ?? '—')),
                    Placeholder::make('mobile_number')
                        ->label(false)
                        ->content(fn($record) => 'Mobile Number - ' . ($record->mobile_number ?? '—')),
                    Placeholder::make('phone_number')
                        ->label(false)
                        ->content(fn($record) => 'Phone Number - ' . ($record->phone_number ?? '—')),
                    Placeholder::make('user_language')
                        ->label(false)
                        ->content(fn($record) => 'User Language - ' . ($record->userMeta?->language ?? '—')),

                ]),

            Section::make('Agent Information')
                ->schema([
                    Placeholder::make('agent')
                        ->label(false)
                        ->content(fn($record) => 'Agent - ---'),

                    Placeholder::make('agent_commission')
                        ->label(false)
                        ->content(fn($record) => 'Agent Commission - --- €'),
                ])
                ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'agent'])),


            Section::make('Notes')
                ->schema([

                    Placeholder::make('log_events')
                        ->label('Reservation notes - Log Events')
                        ->content(function ($state, $record) {
                            $events = is_array($record->log_events)
                                ? $record->log_events
                                : json_decode($record->log_events, true);

                            if (empty($events)) {
                                return 'No log events.';
                            }

                            $html = '<div style="font-size: 0.875rem; line-height: 1.4;">';

                            foreach ($events as $event) {
                                $time = $event['event_time'] ?? '-';
                                $code = $event['event'] ?? '-';
                                $comment = $event['comment'] ?? '';
                                $admin = $event['admin_id'] ?? '-';

                                $html .= "<p><strong>{$time}</strong> ";
                                if ($comment) {
                                    $html .= 'Comment - ' . htmlspecialchars($comment);
                                } else {
                                    $html .= '<em>No comment</em>';
                                }
                                $html .= " <span style='color: #999'>(Admin: {$admin})</span></p>";
                            }

                            $html .= '</div>';

                            return new HtmlString($html);
                        }),
                    Placeholder::make('notifications_sent')
                        ->label('Reservation notes - Notifications Sent')
                        ->content(function ($state, $record) {
                            $notifications = is_array($record->notifications_sent)
                                ? $record->notifications_sent
                                : json_decode($record->notifications_sent, true);

                            if (empty($notifications)) {
                                return 'No notifications.';
                            }

                            $html = '<div style="font-size: 0.875rem; line-height: 1.4;">';

                            foreach ($notifications as $notification) {
                                $time = $notification['time'] ?? '-';
                                $notes = $notification['notes'] ?? '';
                                $medium = $notification['medium'] ?? '-';
                                $status = $notification['status'] ?? '-';
                                $type = $notification['notification_type'] ?? '-';

                                $html .= "<p><strong>{$time}</strong> ";
                                // $html .= "[{$type}] ";
                                $html .= htmlspecialchars($notes);
                                // $html .= " <span style='color: #999'>(Status: {$status}, Medium: {$medium})</span></p>";
                            }

                            $html .= '</div>';

                            return new HtmlString($html);
                        }),

                    Placeholder::make('admin_notes')
                        ->label(false)
                        ->content(fn($record) => 'Private admin notes - ' . ($record->admin_notes ?? '—'))
                        ->visible(fn() => auth()->user()?->hasAnyRole(['admin'])),

                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('start_time', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('planyo_reservation_id')
                    ->label('Reservation ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Property')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('User')
                    ->searchable(['first_name', 'last_name'])
                    ->getStateUsing(fn($record) => "{$record->first_name} {$record->last_name}"),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(
                        fn($record) =>
                        "{$record->start_time->format('d.m.Y')} - (" .
                            $record->start_time->diffInDays($record->end_time) . " nights)"
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $statuses = ReservationStatus::decode($state);
                        return end($statuses); // возвращает последний элемент массива
                    }),

                Tables\Columns\TextColumn::make('agent_name')
                    ->label('Agent')
                    ->getStateUsing(fn($record) => $record->agent ? "{$record->agent->first_name} {$record->agent->last_name}" : '-'),
                // ->hidden(fn() => !auth()->user()->isAdmin()), // Отображается только администратору

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        ReservationStatus::COMPLETED          => 'Reservation completed',
                        ReservationStatus::EMAIL_VERIFIED     => 'Email verified',
                        ReservationStatus::CONFIRMED          => 'Reservation confirmed',
                        ReservationStatus::CANCELLED_BY_ADMIN => 'Cancelled by administrator',
                        ReservationStatus::CANCELLED_BY_USER  => 'Cancelled by user',
                        ReservationStatus::FRAUDULENT         => 'Fraudulent reservation',
                        ReservationStatus::CONFLICT           => 'Conflict',
                        ReservationStatus::UNAVAILABLE        => 'Does not affect availability',
                        ReservationStatus::CANCELLED_AUTO     => 'Cancelled automatically',
                        ReservationStatus::QUOTATION          => 'Quotation',
                        ReservationStatus::WAITING_LIST       => 'Waiting list request',
                        ReservationStatus::LOCKED             => 'Locked for modifications',
                        ReservationStatus::CHECKED_IN         => 'Client checked in',
                        ReservationStatus::CHECKED_OUT        => 'Client checked out',
                        ReservationStatus::NO_SHOW            => 'No-show',
                    ])
                    ->query(
                        fn(Builder $query, $data) =>
                        !empty($data['value']) ? $query->whereRaw("status & ? > 0", [$data]) : $query
                    ),

                Filter::make('dateRange')
                    ->form([
                        DatePicker::make('start')->label('Start date'),
                        DatePicker::make('end')->label('End date'),
                    ])
                    ->query(
                        fn(Builder $query, array $data) =>
                        $query->when(!empty($data['start']), fn($q) => $q->whereDate('start_time', '>=', $data['start']))
                            ->when(!empty($data['end']), fn($q) => $q->whereDate('end_time', '<=', $data['end']))
                    )
                    ->label('Date range'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                EditAction::make()
                    ->label('View')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(
                        fn(StaticAction $action) =>
                        $action->label('Ok')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReservations::route('/'),
        ];
    }
}
