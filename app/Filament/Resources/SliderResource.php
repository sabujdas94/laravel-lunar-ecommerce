<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Sliders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slider Content')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->image()
                            ->directory('sliders')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('heading')
                            ->maxLength(191)
                            ->label('Heading'),

                        Forms\Components\TextInput::make('sub_heading')
                            ->maxLength(191)
                            ->label('Sub Heading'),

                        Forms\Components\TextInput::make('tag')
                            ->maxLength(191)
                            ->label('Tag (e.g., "NEW", "SALE")')
                            ->placeholder('SALE'),

                        Forms\Components\Select::make('tag_style')
                            ->options([
                                1 => 'Style 1 (Primary)',
                                2 => 'Style 2 (Secondary)',
                                3 => 'Style 3 (Accent)',
                            ])
                            ->default(1)
                            ->label('Tag Style')
                            ->helperText('Choose visual style for the tag'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Call to Action Buttons')
                    ->schema([
                        Forms\Components\TextInput::make('button1_label')
                            ->maxLength(191)
                            ->label('Button 1 Label')
                            ->placeholder('Shop Now'),

                        Forms\Components\TextInput::make('button1_url')
                            ->url()
                            ->maxLength(191)
                            ->label('Button 1 URL')
                            ->placeholder('https://example.com/shop'),

                        Forms\Components\TextInput::make('button2_label')
                            ->maxLength(191)
                            ->label('Button 2 Label')
                            ->placeholder('Learn More'),

                        Forms\Components\TextInput::make('button2_url')
                            ->url()
                            ->maxLength(191)
                            ->label('Button 2 URL')
                            ->placeholder('https://example.com/about'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this slider'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Availability Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Leave empty for immediate availability'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Leave empty for no expiration')
                            ->after('start_date'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(60),

                Tables\Columns\TextColumn::make('heading')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                Tables\Columns\TextColumn::make('sub_heading')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tag')
                    ->badge()
                    ->color(fn ($record) => match($record->tag_style) {
                        1 => 'primary',
                        2 => 'success',
                        3 => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('button1_label')
                    ->label('Button 1')
                    ->toggleable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('button2_label')
                    ->label('Button 2')
                    ->toggleable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Always'),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Never'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),

                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled')
                    ->query(fn ($query) => $query->whereNotNull('start_date')
                        ->orWhereNotNull('end_date')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
