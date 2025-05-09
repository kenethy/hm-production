<?php

// Script untuk menonaktifkan fitur backup otomatis

echo "===================================================\n";
echo "     MENONAKTIFKAN FITUR BACKUP OTOMATIS           \n";
echo "===================================================\n\n";

// Fungsi untuk mencari dan memodifikasi file konfigurasi
function findAndModifyConfigFiles($directory, $pattern, $replacement) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    $modifiedFiles = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);
            
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, $replacement, $content);
                
                if ($content !== $newContent) {
                    file_put_contents($filePath, $newContent);
                    $modifiedFiles[] = $filePath;
                }
            }
        }
    }
    
    return $modifiedFiles;
}

// 1. Menonaktifkan backup di Filament
echo ">> Menonaktifkan backup di Filament...\n";

// Cari file konfigurasi Filament
$filamentConfigPath = __DIR__ . '/config/filament.php';
if (file_exists($filamentConfigPath)) {
    $content = file_get_contents($filamentConfigPath);
    
    // Periksa apakah ada konfigurasi backup
    if (strpos($content, 'backup') !== false) {
        // Nonaktifkan backup
        $content = preg_replace(
            "/'backup' => true,/",
            "'backup' => false,",
            $content
        );
        
        file_put_contents($filamentConfigPath, $content);
        echo "Backup di Filament berhasil dinonaktifkan.\n";
    } else {
        echo "Tidak ditemukan konfigurasi backup di Filament.\n";
    }
} else {
    echo "File konfigurasi Filament tidak ditemukan.\n";
}
echo "\n";

// 2. Menonaktifkan backup di IDE Helper
echo ">> Menonaktifkan backup di IDE Helper...\n";

$ideHelperConfigPath = __DIR__ . '/config/ide-helper.php';
if (file_exists($ideHelperConfigPath)) {
    $content = file_get_contents($ideHelperConfigPath);
    
    // Periksa apakah ada konfigurasi backup
    if (strpos($content, 'write_model_magic_where') !== false) {
        // Nonaktifkan backup
        $content = preg_replace(
            "/'write_model_magic_where' => true,/",
            "'write_model_magic_where' => false,",
            $content
        );
        
        file_put_contents($ideHelperConfigPath, $content);
        echo "Backup di IDE Helper berhasil dinonaktifkan.\n";
    } else {
        echo "Tidak ditemukan konfigurasi backup di IDE Helper.\n";
    }
} else {
    echo "File konfigurasi IDE Helper tidak ditemukan.\n";
}
echo "\n";

// 3. Menonaktifkan backup di Laravel Backup
echo ">> Menonaktifkan backup di Laravel Backup...\n";

$backupConfigPath = __DIR__ . '/config/backup.php';
if (file_exists($backupConfigPath)) {
    $content = file_get_contents($backupConfigPath);
    
    // Periksa apakah ada konfigurasi backup
    if (strpos($content, 'backup') !== false) {
        // Nonaktifkan backup
        $content = preg_replace(
            "/'enabled' => true,/",
            "'enabled' => false,",
            $content
        );
        
        file_put_contents($backupConfigPath, $content);
        echo "Backup di Laravel Backup berhasil dinonaktifkan.\n";
    } else {
        echo "Tidak ditemukan konfigurasi backup di Laravel Backup.\n";
    }
} else {
    echo "File konfigurasi Laravel Backup tidak ditemukan.\n";
}
echo "\n";

// 4. Menonaktifkan backup di file-file lain
echo ">> Menonaktifkan backup di file-file lain...\n";

// Cari dan modifikasi file yang mungkin membuat backup
$modifiedFiles = findAndModifyConfigFiles(
    __DIR__ . '/app',
    '/->backup\(\s*true\s*\)/',
    '->backup(false)'
);

if (count($modifiedFiles) > 0) {
    echo "Backup dinonaktifkan di file-file berikut:\n";
    foreach ($modifiedFiles as $file) {
        echo "  - " . str_replace(__DIR__ . '/', '', $file) . "\n";
    }
} else {
    echo "Tidak ditemukan file lain yang menggunakan fitur backup.\n";
}
echo "\n";

// 5. Menonaktifkan pembuatan file backup di .history
echo ">> Menonaktifkan pembuatan file backup di .history...\n";

// Buat file .gitignore untuk mengabaikan direktori .history
$gitignorePath = __DIR__ . '/.gitignore';
if (file_exists($gitignorePath)) {
    $content = file_get_contents($gitignorePath);
    
    if (strpos($content, '.history') === false) {
        file_put_contents($gitignorePath, $content . "\n# Ignore history files\n.history/\n");
        echo "Direktori .history ditambahkan ke .gitignore.\n";
    } else {
        echo "Direktori .history sudah ada di .gitignore.\n";
    }
} else {
    file_put_contents($gitignorePath, "# Ignore history files\n.history/\n");
    echo "File .gitignore dibuat dengan pengabaian direktori .history.\n";
}

// Buat file .history/.gitignore untuk mengosongkan direktori .history
if (!is_dir(__DIR__ . '/.history')) {
    mkdir(__DIR__ . '/.history', 0755, true);
}
file_put_contents(__DIR__ . '/.history/.gitignore', "*\n!.gitignore\n");
echo "File .gitignore dibuat di direktori .history untuk mengosongkan direktori.\n";
echo "\n";

// 6. Menonaktifkan pembuatan file backup di editor
echo ">> Menonaktifkan pembuatan file backup di editor...\n";

// Buat file .editorconfig untuk menonaktifkan backup
$editorConfigPath = __DIR__ . '/.editorconfig';
$editorConfigContent = <<<EOD
root = true

[*]
charset = utf-8
end_of_line = lf
insert_final_newline = true
indent_style = space
indent_size = 4
trim_trailing_whitespace = true

[*.md]
trim_trailing_whitespace = false

[*.{yml,yaml,json,js,css,scss,vue}]
indent_size = 2

# Disable backup files
[*]
trim_trailing_whitespace = true
insert_final_newline = true
max_line_length = off
create_backup = false

EOD;

file_put_contents($editorConfigPath, $editorConfigContent);
echo "File .editorconfig dibuat/diperbarui untuk menonaktifkan pembuatan backup.\n";
echo "\n";

echo "===================================================\n";
echo "     PENONAKTIFAN SELESAI                          \n";
echo "===================================================\n\n";

echo "Fitur backup otomatis telah dinonaktifkan.\n";
echo "Untuk membersihkan file backup yang sudah ada, jalankan script clean-backup-files.sh\n";
