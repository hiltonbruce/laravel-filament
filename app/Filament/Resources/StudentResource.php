<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Section;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->live()
                    ->relationship(name: 'class', titleAttribute: 'name'),
                Select::make('section_id')
                    ->label('Section')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        if ($classId) {
                            return Section::where('class_id', $classId)->pluck('name', 'id')->toArray();
                        }
                        // info($classId);
                    }),
                TextInput::make('name')
                    ->required()
                    ->autofocus(true),
                TextInput::make('email')
                    ->required()
                    ->unique(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('class.name')->badge()->searchable()->sortable(),
                TextColumn::make('section.name')
                        ->sortable()
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'draft' => 'gray',
                            'reviewing' => 'warning',
                            'Section A' => 'success',
                            'Section B' => 'danger',
                        }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
