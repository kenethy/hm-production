<?php

// Script untuk mencari dan menghapus file PHP dengan duplikat method

echo "===================================================\n";
echo "     PENCARIAN FILE DENGAN DUPLIKAT METHOD         \n";
echo "===================================================\n\n";

// Fungsi untuk mencari file PHP
function findPhpFiles($directory) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    $phpFiles = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $phpFiles[] = $file->getPathname();
        }
    }
    
    return $phpFiles;
}

// Fungsi untuk mencari duplikat method dalam file PHP
function findDuplicateMethods($filePath) {
    $content = file_get_contents($filePath);
    
    // Cari semua definisi method
    preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $matches, PREG_OFFSET_CAPTURE);
    
    if (empty($matches[1])) {
        return [];
    }
    
    $methods = [];
    $duplicates = [];
    
    foreach ($matches[1] as $match) {
        $methodName = $match[0];
        $position = $match[1];
        
        if (isset($methods[$methodName])) {
            $duplicates[$methodName] = [
                'name' => $methodName,
                'first_position' => $methods[$methodName],
                'duplicate_position' => $position,
            ];
        } else {
            $methods[$methodName] = $position;
        }
    }
    
    return $duplicates;
}

// Fungsi untuk memeriksa apakah file adalah backup
function isBackupFile($filePath) {
    $fileName = basename($filePath);
    
    // Periksa pola nama file backup
    if (preg_match('/\.(bak|backup|save|old|orig|copy)$/', $fileName)) {
        return true;
    }
    
    // Periksa pola nama file dengan tanggal
    if (preg_match('/_[0-9]{8,14}\.[a-zA-Z0-9]+$/', $fileName)) {
        return true;
    }
    
    return false;
}

// Direktori root aplikasi
$rootDir = __DIR__;

echo "Mencari file PHP di direktori: $rootDir\n";
$phpFiles = findPhpFiles($rootDir);
echo "Ditemukan " . count($phpFiles) . " file PHP.\n\n";

// Mencari file dengan duplikat method
echo "Mencari file dengan duplikat method...\n";
$filesWithDuplicates = [];
$backupFilesWithDuplicates = [];

foreach ($phpFiles as $filePath) {
    $duplicates = findDuplicateMethods($filePath);
    
    if (!empty($duplicates)) {
        $isBackup = isBackupFile($filePath);
        $fileInfo = [
            'path' => $filePath,
            'duplicates' => $duplicates,
            'is_backup' => $isBackup,
        ];
        
        if ($isBackup) {
            $backupFilesWithDuplicates[] = $fileInfo;
        } else {
            $filesWithDuplicates[] = $fileInfo;
        }
    }
}

// Tampilkan hasil
if (empty($filesWithDuplicates) && empty($backupFilesWithDuplicates)) {
    echo "Tidak ditemukan file dengan duplikat method.\n";
} else {
    // Tampilkan file backup dengan duplikat method
    if (!empty($backupFilesWithDuplicates)) {
        echo "\nDitemukan " . count($backupFilesWithDuplicates) . " file BACKUP dengan duplikat method:\n";
        
        foreach ($backupFilesWithDuplicates as $fileInfo) {
            $relativePath = str_replace($rootDir . '/', '', $fileInfo['path']);
            echo "- $relativePath\n";
            
            foreach ($fileInfo['duplicates'] as $duplicate) {
                echo "  * Method '{$duplicate['name']}' diduplikasi\n";
            }
        }
        
        // Tanyakan apakah ingin menghapus file backup dengan duplikat method
        echo "\nApakah Anda ingin menghapus file backup dengan duplikat method? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) === 'y') {
            foreach ($backupFilesWithDuplicates as $fileInfo) {
                unlink($fileInfo['path']);
                echo "Menghapus: {$fileInfo['path']}\n";
            }
            echo "Semua file backup dengan duplikat method telah dihapus.\n";
        } else {
            echo "Tidak ada file yang dihapus.\n";
        }
    }
    
    // Tampilkan file non-backup dengan duplikat method
    if (!empty($filesWithDuplicates)) {
        echo "\nDitemukan " . count($filesWithDuplicates) . " file NON-BACKUP dengan duplikat method:\n";
        
        foreach ($filesWithDuplicates as $fileInfo) {
            $relativePath = str_replace($rootDir . '/', '', $fileInfo['path']);
            echo "- $relativePath\n";
            
            foreach ($fileInfo['duplicates'] as $duplicate) {
                echo "  * Method '{$duplicate['name']}' diduplikasi\n";
            }
        }
        
        echo "\nPERINGATAN: File-file ini bukan file backup, tetapi memiliki duplikat method.\n";
        echo "Anda perlu memeriksa dan memperbaiki file-file ini secara manual.\n";
    }
}

echo "\n===================================================\n";
echo "     PENCARIAN SELESAI                             \n";
echo "===================================================\n\n";

echo "Untuk membersihkan semua file backup, jalankan script clean-backup-files.sh\n";
echo "Untuk menonaktifkan fitur backup otomatis, jalankan script disable-auto-backup.php\n";
