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
                $customer = Customer::create([
                    'name' => $service->customer_name,
                    'phone' => $service->phone,
                    'is_active' => true,
                ]);

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
