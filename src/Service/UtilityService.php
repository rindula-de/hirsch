<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Service;

class UtilityService
{
    /**
     * Generate an MD5 hash string from the contents of a directory.
     *
     * @param string $directory
     */
    public function hashDirectory($directory): bool|string
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = [];
        $dir = dir($directory);

        if ($dir) {
            while (false !== ($file = $dir->read())) {
                if ('.' !== $file and '..' !== $file) {
                    if (is_dir($directory.'/'.$file)) {
                        $files[] = $this->hashDirectory($directory.'/'.$file);
                    } else {
                        $files[] = md5_file($directory.'/'.$file);
                    }
                }
            }

            $dir->close();
        }

        return md5(implode('', $files));
    }
}
