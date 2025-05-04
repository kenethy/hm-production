<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('uploadMultiple')
                ->label('Upload Batch')
                ->icon('heroicon-o-photo')
                ->form([
                    Select::make('category_id')
                        ->label('Kategori')
                        ->options(GalleryCategory::pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    FileUpload::make('images')
                        ->label('Gambar (Multiple)')
                        ->multiple()
                        ->image()
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('16:9')
                        ->imageResizeTargetWidth('1200')
                        ->imageResizeTargetHeight('675')
                        ->directory('gallery')
                        ->visibility('public')
                        ->required(),

                    TextInput::make('title_prefix')
                        ->label('Awalan Judul')
                        ->helperText('Akan ditambahkan nomor urut di belakangnya')
                        ->required(),

                    Toggle::make('is_featured')
                        ->label('Tampilkan di Halaman Utama')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $category = GalleryCategory::findOrFail($data['category_id']);
                    $titlePrefix = $data['title_prefix'];
                    $isFeatured = $data['is_featured'] ?? false;

                    $count = 0;
                    foreach ($data['images'] as $imagePath) {
                        $count++;

                        Gallery::create([
                            'title' => $titlePrefix . ' ' . $count,
                            'description' => 'Foto ' . $titlePrefix . ' ' . $count . ' - Kategori: ' . $category->name,
                            'image_path' => $imagePath,
                            'category_id' => $category->id,
                            'is_featured' => $isFeatured,
                            'order' => Gallery::where('category_id', $category->id)->count() + 1,
                        ]);
                    }

                    Notification::make()
                        ->title('Berhasil mengupload ' . $count . ' foto')
                        ->success()
                        ->send();
                }),
        ];
    }
}
