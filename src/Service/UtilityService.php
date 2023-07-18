<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Service;

class UtilityService
{
    /**
     * Generate an MD5 hash string from the contents of a directory.
     */
    public function hashDirectory(string $directory): bool|string
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = [];
        $dir = dir($directory);

        if ($dir) {
            while (false !== ($file = $dir->read())) {
                if ('.' !== $file && '..' !== $file) {
                    if (is_dir(sprintf('%s/%s', $directory, $file))) {
                        $files[] = $this->hashDirectory(sprintf('%s/%s', $directory, $file));
                    } else {
                        $files[] = md5_file(sprintf('%s/%s', $directory, $file));
                    }
                }
            }

            $dir->close();
        }

        if (empty($files)) {
            return false;
        }

        return md5(implode('', $files));
    }
}
