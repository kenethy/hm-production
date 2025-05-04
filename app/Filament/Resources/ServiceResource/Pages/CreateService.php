<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function afterCreate(): void
    {
        $service = $this->record;

        Log::info('afterCreate called for service', [
            'service_id' => $service->id,
            'customer_id' => $service->customer_id,
            'customer_name' => $service->customer_name,
            'phone' => $service->phone,
        ]);

        // Check if customer_id is not set but we have customer_name and phone
        if (!$service->customer_id && $service->customer_name && $service->phone) {
            // Check if customer exists with this phone number
            $customer = Customer::where('phone', $service->phone)->first();

            Log::info('Checking for existing customer', [
                'phone' => $service->phone,
                'customer_exists' => $customer ? 'yes' : 'no',
                'customer_id' => $customer ? $customer->id : null,
            ]);

            if ($customer) {
                // If customer exists, associate service with this customer
                $service->customer_id = $customer->id;
                $service->save();

                Notification::make()
                    ->title('Service berhasil dikaitkan dengan pelanggan yang sudah ada')
                    ->success()
                    ->send();
            } else {
                // If customer doesn't exist, create a new one
                try {
                    $customer = Customer::create([
                        'name' => $service->customer_name,
                        'phone' => $service->phone,
                        'is_active' => true,
                    ]);

                    Log::info('New customer created', [
                        'customer_id' => $customer->id,
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error creating customer', [
                        'error' => $e->getMessage(),
                        'name' => $service->customer_name,
                        'phone' => $service->phone,
                    ]);

                    Notification::make()
                        ->title('Error membuat pelanggan baru: ' . $e->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                // Associate service with the new customer
                $service->customer_id = $customer->id;
                $service->save();

                Notification::make()
                    ->title('Pelanggan baru berhasil dibuat dan dikaitkan dengan service')
                    ->success()
                    ->send();
            }
        }
    }
}
