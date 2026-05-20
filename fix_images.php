<?php

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('D:\TFG_Projet\front_back_ecomerce\Projet_Presentia\Presentia\resources\views'));
foreach ($dir as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        $newContent = preg_replace('/src="assets\/([^"]+)"/', 'src="{{ asset(\'assets/$1\') }}"', $content);
        $newContent = str_replace('Storage::url($user->photo)', 'asset(\'storage/\' . $user->photo)', $newContent);
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}
echo "Done.\n";
