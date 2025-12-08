<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoPopupResource\Pages;
use App\Models\PromoPopup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoPopupResource extends Resource
{
    protected static ?string $model = PromoPopup::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Promo Popup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Popup Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(191)
                            ->label('Popup Title'),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(500)
                            ->label('Description'),

                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('promo-popups')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->label('Banner/Image')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('button_text')
                            ->maxLength(191)
                            ->placeholder('Shop Now')
                            ->label('Button Text'),

                        Forms\Components\TextInput::make('link')
                            ->url()
                            ->maxLength(191)
                            ->placeholder('https://example.com/promo')
                            ->label('Button Link'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Availability')
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

                        Forms\Components\Toggle::make('is_enabled')
                            ->label('Enabled')
                            ->default(true)
                            ->helperText('Enable or disable this popup'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('button_text')
                    ->searchable()
                    ->toggleable()
                    ->label('Button'),

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

                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean()
                    ->label('Enabled')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->trueLabel('Enabled only')
                    ->falseLabel('Disabled only')
                    ->native(false),

                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled')
                    ->query(fn ($query) => $query->whereNotNull('start_date')
                        ->orWhereNotNull('end_date')),

                Tables\Filters\Filter::make('active')
                    ->label('Currently Active')
                    ->query(fn ($query) => $query->active()),
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
            'index' => Pages\ListPromoPopups::route('/'),
            'create' => Pages\CreatePromoPopup::route('/create'),
            'edit' => Pages\EditPromoPopup::route('/{record}/edit'),
        ];
    }
}
