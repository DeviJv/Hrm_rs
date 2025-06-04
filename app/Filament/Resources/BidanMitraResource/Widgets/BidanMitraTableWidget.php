<?php

namespace App\Filament\Resources\BidanMitraResource\Widgets;

use Filament\Tables;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;

class BidanMitraTableWidget extends MapTableWidget
{
	protected static ?string $heading = 'Location Map';

	protected static ?int $sort = 1;

	protected static ?string $pollingInterval = null;

	protected static ?bool $clustering = true;

	protected static ?string $mapId = 'incidents';

	protected function getTableQuery(): Builder
	{
		return Location::query()->latest();
	}

	protected function getTableColumns(): array
	{
		return [
			Tables\Columns\TextColumn::make('lat'),
			Tables\Columns\TextColumn::make('lang'),
			MapColumn::make('location')
				->extraImgAttributes(
					fn($record): array => ['title' => $record->lat . ',' . $record->lang]
				)
				->height('150')
				->width('250')
				->type('hybrid')
				->zoom(15),
		];
	}

	protected function getTableFilters(): array
	{
		return [
			RadiusFilter::make('nama')
				->section('Radius Filter')
				->selectUnit(),
			MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\ViewAction::make(),
			Tables\Actions\EditAction::make(),
			GoToAction::make()
				->zoom(14),
			RadiusAction::make(),
		];
	}

	protected function getData(): array
	{
		$locations = $this->getRecords();

		$data = [];

		foreach ($locations as $location) {
			$data[] = [
				'location' => [
					'lat' => $location->lat ? round(floatval($location->lat), static::$precision) : 0,
					'lng' => $location->lang ? round(floatval($location->lang), static::$precision) : 0,
				],
				'id'      => $location->id,
			];
		}

		return $data;
	}
}
