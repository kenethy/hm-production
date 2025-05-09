/**
 * Script untuk memicu tampilan biaya jasa montir secara otomatis
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Mechanic costs fix script loaded');
    
    // Tunggu Livewire dan Alpine.js dimuat
    setTimeout(function() {
        triggerMechanicCostsDisplay();
    }, 1000);
});

/**
 * Fungsi untuk memicu tampilan biaya jasa montir
 */
function triggerMechanicCostsDisplay() {
    console.log('Triggering mechanic costs display');
    
    // Cari elemen select montir
    const mechanicsSelect = document.querySelector('[wire\\:model="data.mechanics"]');
    
    if (mechanicsSelect) {
        console.log('Found mechanics select element');
        
        // Dapatkan nilai saat ini
        const currentValue = mechanicsSelect.value;
        
        // Simulasikan perubahan nilai
        const event = new Event('change', { bubbles: true });
        mechanicsSelect.dispatchEvent(event);
        
        // Trigger Livewire update
        if (window.Livewire) {
            console.log('Triggering Livewire update');
            
            // Coba berbagai pendekatan untuk memicu update Livewire
            try {
                // Pendekatan 1: Gunakan API Livewire
                const componentId = mechanicsSelect.closest('[wire\\:id]')?.getAttribute('wire:id');
                if (componentId) {
                    console.log('Found Livewire component ID:', componentId);
                    window.Livewire.find(componentId).set('data.mechanics', currentValue);
                }
            } catch (e) {
                console.error('Error triggering Livewire update:', e);
            }
        }
        
        // Cek apakah ada elemen repeater untuk biaya jasa
        const repeaterElement = document.querySelector('.fi-fo-repeater');
        if (!repeaterElement || repeaterElement.children.length === 0) {
            console.log('Mechanic costs repeater not found or empty, trying alternative approach');
            
            // Coba pendekatan alternatif: Klik pada elemen select
            try {
                mechanicsSelect.click();
                setTimeout(() => {
                    mechanicsSelect.click();
                }, 500);
            } catch (e) {
                console.error('Error clicking mechanics select:', e);
            }
        } else {
            console.log('Mechanic costs repeater found with children:', repeaterElement.children.length);
        }
    } else {
        console.log('Mechanics select element not found, trying to find by name');
        
        // Coba cari elemen dengan nama yang mungkin berbeda
        const possibleSelectors = [
            '[name="data[mechanics][]"]',
            '[id*="data.mechanics"]',
            'select[multiple]'
        ];
        
        for (const selector of possibleSelectors) {
            const element = document.querySelector(selector);
            if (element) {
                console.log('Found alternative mechanics element with selector:', selector);
                
                // Simulasikan perubahan nilai
                const event = new Event('change', { bubbles: true });
                element.dispatchEvent(event);
                
                // Coba klik pada elemen
                try {
                    element.click();
                    setTimeout(() => {
                        element.click();
                    }, 500);
                } catch (e) {
                    console.error('Error clicking alternative mechanics element:', e);
                }
                
                break;
            }
        }
    }
    
    // Coba lagi setelah beberapa detik jika masih belum muncul
    setTimeout(function() {
        const mechanicCostsElement = document.querySelector('[wire\\:key*="mechanic_costs"]');
        if (!mechanicCostsElement) {
            console.log('Mechanic costs element still not found, trying again');
            triggerMechanicCostsDisplay();
        } else {
            console.log('Mechanic costs element found:', mechanicCostsElement);
        }
    }, 2000);
}
