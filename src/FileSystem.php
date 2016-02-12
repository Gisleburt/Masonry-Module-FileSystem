<?php
/**
 * FileSystem.php
 *
 * @author    Daniel Mason <daniel.mason@thefoundry.co.uk>
 * @copyright 2015 The Foundry Visionmongers
 * @license
 * @see       https://github.com/Visionmongers/Masonry-Builder
 */


namespace Foundry\Masonry\Module\FileSystem;

/**
 * Class FileSystem
 * Wraps file system functionality
 * @package Foundry\Masonry-Builder
 * @see     https://github.com/Visionmongers/Masonry-Builder
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class FileSystem
{
    /**
     * Wraps copy and is able to copy directories
     * @param string $from Source location
     * @param string $to   Target location
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return bool
     */
    public function copy($from, $to)
    {
        if ($this->isFile($from)) {
            if (@copy($from, $to)) {
                return true;
            }
            throw new \Exception("Could not copy file '$from' to '$to'");
        }

        if ($this->isDirectory($from)) {
            $returnValue = true;

            // Does the "to" directory need to be created
            $makingDirectory = !$this->isDirectory($to);
            if ($makingDirectory) {
                if (!$this->makeDirectory($to, 0777, true)) {
                    throw new \Exception("Could not create directory '$to'");
                }
            }

            // Step through the directory
            $fromDirectory = opendir($from);
            while (false !== ($file = readdir($fromDirectory))) {
                if (($file != '.') && ($file != '..')) {
                    $returnValue = $returnValue && $this->copy("$from/$file", "$to/$file");
                }
            }

            closedir($fromDirectory);

            // Fix permissions
            if ($makingDirectory) {
                $returnValue = $returnValue && $this->changeMode($to, fileperms($from));
            }

            return $returnValue;
        }

        throw new \Exception("'$from' does not exist or is not accessible");
    }

    /**
     * Write to file.
     * If the directory the file wants to be in does not already exist, it is created with permission 0755. That is
     * read and execute for group and other, read, write and execute for owner.
     *
     * @param string $target   File path to write to
     * @param mixed  $contents Contents to write
     *
     * @return true
     *
     * @throws \Exception
     */
    public function write($target, $contents)
    {
        // is it a file name of a path including path separators?
        $directoryBit = dirname($target);
        // if it is not dot - current directory - then there is a directory path in the $target
        if ($directoryBit != ".") {
        // Ensure the directory exists
            if (!$this->isDirectory($directoryBit)) {
                $this->makeDirectory($directoryBit, 0755, true);
            }
        }

        if (file_put_contents($target, $contents) === false) {
            throw new \Exception("Could not write to '$target'");
        }

        return true;
    }

    /**
     * Wraps unlink and rmdir to recursively delete a file or directory
     * Warning: This will delete every thing that _can_ be deleted. If a single file can't be deleted, everything but
     * it's containing directories will still be deleted.
     * @param string $fileOrDirectory The file or directory to be deleted
     * @return bool
     */
    public function delete($fileOrDirectory)
    {
        if ($this->isSymlink($fileOrDirectory) || $this->isFile($fileOrDirectory)) {
            return @unlink($fileOrDirectory);
        }
        if ($this->isDirectory($fileOrDirectory)) {
            $directory = opendir($fileOrDirectory);
            while (false !== ($file = readdir($directory))) {
                if (($file != '.') && ($file != '..')) {
                    // The return of any child deletes doesn't matter as rmdir won't complete below.
                    $this->delete("$fileOrDirectory/$file");
                }
            }
            closedir($directory);

            return @rmdir($fileOrDirectory);
        }
        return false;
    }

    /**
     * Wraps mkdir
     * This method is always recursive.
     * @param string $directory The name of the directory to be created
     * @param int    $mode      Defaults to 0777
     * @return bool
     */
    public function makeDirectory($directory, $mode = 0777)
    {
        return @mkdir($directory, $mode, true);
    }

    /**
     * Wraps rename
     * @param string $from Source location
     * @param string $to   Target location
     * @return bool
     */
    public function move($from, $to)
    {
        return @rename($from, $to);
    }

    /**
     * Wraps is_dir
     * @param $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return @is_dir($directory);
    }

    /**
     * Wraps is_file
     * @param $file
     * @return bool
     */
    public function isFile($file)
    {
        return @is_file($file);
    }

    /**
     * Wraps is_link
     * @param $file
     * @return bool
     */
    public function isSymlink($file)
    {
        return @is_link($file);
    }

    /**
     * Change mode (or chmod)
     * @param string $fileOrDirectory The file or directory to modify
     * @param int $octalMode The flags to set (should be given in octal to avoid problems)
     * @return bool
     */
    public function changeMode($fileOrDirectory, $octalMode)
    {
        return @chmod($fileOrDirectory, $octalMode);
    }

    /**
     * Change owner (or chown)
     * @param string $fileOrDirectory The file or directory to modify
     * @param int $user The user who should have ownership
     * @return bool
     */
    public function changeOwner($fileOrDirectory, $user)
    {
        // Data validation
        if (!$user) {
            throw new \InvalidArgumentException('$user can not be empty');
        }
        if (!$fileOrDirectory) {
            throw new \InvalidArgumentException('$fileOrDirectory can not be empty');
        }

        $parts = explode(":", $user);
        if (count($parts) > 2) {
            throw new \InvalidArgumentException('Badly formatted $user, should only contain 1 colon');
        }

        // If group needs to change too
        if (isset($parts[1])) {
            return $this->changeGroup($fileOrDirectory, $parts[1])
                && @chown($fileOrDirectory, $parts[0]);
        }

        // Otherwise
        return @chown($fileOrDirectory, $user);
    }

    /**
     * Change group (or chgrp)
     * @param string $fileOrDirectory The file or directory to modify
     * @param int $group The group the file or directory belongs to
     * @return bool
     */
    public function changeGroup($fileOrDirectory, $group)
    {
        // Data validation
        if (!$group) {
            throw new \InvalidArgumentException('$group can not be empty');
        }
        if (!$fileOrDirectory) {
            throw new \InvalidArgumentException('$fileOrDirectory can not be empty');
        }

        return @chgrp($fileOrDirectory, $group);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getFileContents($filename)
    {
        $contents = @file_get_contents($filename);
        if ($contents === false) {
            throw new \RuntimeException("'$filename' could not be read");
        }
        return $contents;
    }
}
