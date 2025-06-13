<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Encuestas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->label('Título')
                            ->helperText('Mínimo 3 caracteres'),
                        Forms\Components\TextInput::make('description')
                            ->minLength(3)
                            ->maxLength(255)
                            ->label('Descripción')
                            ->helperText('Mínimo 3 caracteres'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Activa'),
                                                                                                Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Fecha de Inicio')
                            ->minDate(today())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('La fecha de inicio no puede ser anterior a la actual'),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('Fecha de Fin')
                            ->minDate(today())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->rules([
                                function (Forms\Get $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $startDate = $get('start_date');
                                        if ($startDate && $value && $value < $startDate) {
                                            $fail('La fecha de fin no puede ser menor que la fecha de inicio.');
                                        }
                                    };
                                },
                            ])
                            ->helperText('La fecha de fin debe ser igual o posterior a la fecha actual'),
                        Forms\Components\TextInput::make('max_votes')
                            ->numeric()
                            ->minValue(1)
                            ->label('Límite de Votos')
                            ->helperText('Dejar en blanco para sin límite'),
                    ])->columns(2),

                Forms\Components\Section::make('Preguntas')
                    ->schema([
                        Forms\Components\Repeater::make('questions')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('text')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->label('Pregunta')
                                    ->helperText('Mínimo 3 caracteres'),
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'single' => 'Opción Única',
                                        'multiple' => 'Opción Múltiple',
                                    ])
                                    ->required()
                                    ->default('single')
                                    ->label('Tipo'),
                                Forms\Components\Toggle::make('is_required')
                                    ->required()
                                    ->label('Obligatoria'),
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Orden'),
                                Forms\Components\Repeater::make('answers')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('text')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Respuesta'),
                                        Forms\Components\Toggle::make('is_other')
                                            ->label('¿Es opción "Otro"?')
                                            ->helperText('Habilita un campo de texto libre para el usuario')
                                            ->live()
                                            ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                if ($state) {
                                                    // Si se activa el toggle y el campo de texto está vacío o es genérico
                                                    $currentText = $get('text');
                                                    if (empty($currentText) || $currentText === 'Otro' || $currentText === 'Otra') {
                                                        $set('text', 'Otro (especificar)');
                                                    }
                                                } else {
                                                    // Si se desactiva el toggle y el texto es el predeterminado
                                                    $currentText = $get('text');
                                                    if ($currentText === 'Otro (especificar)') {
                                                        $set('text', 'Otro');
                                                    }
                                                }
                                            }),
                                        Forms\Components\TextInput::make('order')
                                            ->numeric()
                                            ->default(0)
                                            ->label('Orden'),
                                    ])
                                    ->columns(3)
                                    ->label('Respuestas'),
                            ])
                            ->columns(2)
                            ->label('Preguntas'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Título'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Descripción'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activa'),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Inicio'),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Fin'),
                Tables\Columns\TextColumn::make('total_votes')
                    ->numeric()
                    ->sortable()
                    ->label('Votos'),
                Tables\Columns\TextColumn::make('max_votes')
                    ->numeric()
                    ->sortable()
                    ->label('Límite'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Activas',
                        'inactive' => 'Inactivas',
                        'expired' => 'Expiradas',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'active' => $query->where('is_active', true)
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now()),
                            'inactive' => $query->where('is_active', false),
                            'expired' => $query->where('end_date', '<', now()),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('results')
                    ->url(fn (Survey $record): string => route('survey.results', ['survey' => $record]))
                    ->icon('heroicon-o-chart-bar')
                    ->label('Resultados')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
} 