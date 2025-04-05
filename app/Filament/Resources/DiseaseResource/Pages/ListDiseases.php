<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use Filament\Actions;
use Filament\Pages\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DiseaseResource;

class ListDiseases extends ListRecords
{
    protected static string $resource = DiseaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Add New"),
            Actions\Action::make("Print")
                ->icon("heroicon-s-printer")
                ->color('success')
                ->modalContent(
                    fn($record) => view(
                        'livewire.report-viewer',
                        [
                            'report_iframe' => "http://localhost:60308/CrystalReportMVC/ViewReport?par_value=0"
                        ]

                    )
                )
                ->modalSubmitAction(false)
                ->modalFooterActionsAlignment('right')
                ->modalCancelActionLabel("Close Preview")
                ->modalWidth('5xl')
                ->modalHeading('Print Filter')
                ->slideOver()
                ->modalAlignment(Alignment::Center)
        ];
    }
}
