<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class UserRoleOverview extends BaseWidget
{
    use InteractsWithPageTable;
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $widget = [];
        $roles = Role::with('users')->get();
        foreach ($roles as $v => $r) {
            $widget[$v] = Stat::make('Total ' . $r->name . '', User::with('roles')->get()->filter(
                fn ($user) => $user->roles->where('name', $r->name)->toArray()
            )->count());
        }
        return $widget;
    }
    protected function getTablePage(): string
    {
        return ListUsers::class;
    }
}
