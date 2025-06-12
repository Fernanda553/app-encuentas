<?php

namespace App\Filament\Resources\SurveyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('text')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'single' => 'Single Choice',
                        'multiple' => 'Multiple Choice',
                    ])
                    ->default('single')
                    ->required(),
                Forms\Components\TextInput::make('order_column')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\TextColumn::make('text'),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Manage Answers')
                    ->url(fn ($record): string => url("/admin/questions/{$record->id}/edit"))
                    ->icon('heroicon-o-list-bullet'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 