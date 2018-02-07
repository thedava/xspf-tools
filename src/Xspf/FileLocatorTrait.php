<?php

namespace Xspf;

use Symfony\Component\Console\Output\OutputInterface;

trait FileLocatorTrait
{
    /**
     * @param string $file
     *
     * @return bool
     */
    private function shouldFileBeSkipped($file)
    {
        return (preg_match('/^(Thumbs\.db|.*\.bak)$/', $file))
            ? true
            : false;
    }

    /**
     * @param string          $folder
     * @param OutputInterface $output
     *
     * @return \Generator
     */
    private function locateFiles($folder, OutputInterface $output)
    {
        foreach (new \DirectoryIterator($folder) as $dir) {
            if ($dir->isDir() && !$dir->isDot()) {
                $output->writeln('- ' . $folder . '/' . $dir->getFilename() . ' -> folder', $output::VERBOSITY_DEBUG);
                foreach ($this->locateFiles($folder . '/' . $dir->getFilename(), $output) as $file) {
                    yield $file;
                }
                continue;
            }

            if ($dir->isFile() && !$this->shouldFileBeSkipped($dir->getBasename())) {
                $output->writeln('- ' . $folder . '/' . $dir->getFilename() . ' -> file', $output::VERBOSITY_DEBUG);
                yield $folder . '/' . $dir->getFilename();
            }
        }
    }

    /**
     * @param string          $fileOrFolder
     * @param OutputInterface $output
     *
     * @return \Generator
     */
    protected function getFiles($fileOrFolder, OutputInterface $output)
    {
        if (is_file($fileOrFolder)) {
            $output->writeln('- ' . $fileOrFolder . ' -> file', $output::VERBOSITY_DEBUG);
            yield $fileOrFolder;

        } elseif (is_dir($fileOrFolder)) {
            $output->writeln('- ' . $fileOrFolder . ' -> folder', $output::VERBOSITY_DEBUG);
            foreach ($this->locateFiles($fileOrFolder, $output) as $file) {
                yield $file;
            }
        } else {
            foreach (glob($fileOrFolder, GLOB_BRACE) as $item) {
                if (is_file($item)) {
                    $output->writeln('- ' . $item . ' -> file', $output::VERBOSITY_DEBUG);
                    yield $item;
                } else {
                    $output->writeln('- ' . $item . ' -> folder', $output::VERBOSITY_DEBUG);
                    foreach ($this->locateFiles($item, $output) as $file) {
                        yield $file;
                    }
                }
            }
        }
    }
}
