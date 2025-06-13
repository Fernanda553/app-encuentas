<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSurvey extends CreateRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirigir a la página de creación (misma página) para limpiar campos
        return $this->getResource()::getUrl('create');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('¡Encuesta creada exitosamente!')
            ->body('La encuesta ha sido creada y está lista para usar.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Aquí puedes agregar cualquier lógica adicional antes de crear
        return $data;
    }

    protected function afterCreate(): void
    {
        // Mostrar notificación de éxito personalizada
        $this->getCreatedNotification()?->send();

        // Limpiar completamente el formulario
        $this->form->fill([
            'title' => '',
            'description' => '',
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
            'max_votes' => null,
            'questions' => [],
        ]);

        // Redirigir para asegurar limpieza completa
        $this->redirect($this->getRedirectUrl());
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return Actions\Action::make('createAnother')
            ->label('Crear otra encuesta')
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    public function createAnother(): void
    {
        $this->create();
    }

    protected function getHomeAction(): Actions\Action
    {
        return Actions\Action::make('goHome')
            ->label('Ir al Home')
            ->icon('heroicon-o-home')
            ->color('info')
            ->url('/')
            ->keyBindings(['mod+h']);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAnotherFormAction(),
            $this->getHomeAction(),
            $this->getCancelFormAction(),
        ];
    }
}
