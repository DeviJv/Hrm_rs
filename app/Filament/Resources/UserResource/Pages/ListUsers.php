<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Actions\ImportAction;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    use ExposesTableToWidgets;

    // protected function paginateTableQuery(Builder $query): Paginator
    // {
    //     return $query->fastPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    // }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // ImportAction::make('importUser')
            //     ->label('Import User')
            //     ->color('info')
            //     ->icon('heroicon-o-archive-box-arrow-down')
            //     ->csvDelimiter(',')
            //     ->importer(UserImporter::class),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            // UserResource\Widgets\UserRoleOverview::class,
        ];
    }
}