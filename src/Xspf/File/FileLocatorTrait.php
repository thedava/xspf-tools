<?php

namespace Xspf\File;

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
        return (preg_match('/^(Thumbs\.db|\.DS\_Store)$/i', $file))
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
        $ds = DIRECTORY_SEPARATOR;
        $folder = rtrim($folder, $ds . '/\\');

        try {
            foreach (new \DirectoryIterator($folder) as $dir) {
                if ($dir->isDir() && !$dir->isDot()) {
                    $output->writeln('- ' . $folder . $ds . $dir->getFilename() . ' -> folder', $output::VERBOSITY_DEBUG);
                    foreach ($this->locateFiles($folder . $ds . $dir->getFilename(), $output) as $file) {
                        yield $file;
                    }
                    continue;
                }

                if ($dir->isFile() && !$this->shouldFileBeSkipped($dir->getBasename())) {
                    $output->writeln('- ' . $folder . $ds . $dir->getFilename() . ' -> file', $output::VERBOSITY_DEBUG);
                    yield $folder . $ds . $dir->getFilename();
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function escapeGlobPattern($pattern)
    {
        $pattern = str_replace(['[', ']'], ['\[', '\]'], $pattern);
        $pattern = str_replace(['\[', '\]'], ['[[]', '[]]'], $pattern);

        return $pattern;
    }

    /**
     * @param string          $fileOrFolder
     * @param OutputInterface $output
     *
     * @return \Generator
     */
    protected function getFiles($fileOrFolder, OutputInterface $output)
    {
        if (is_file($fileOrFolder) || preg_match('/.*\.\w*$/', $fileOrFolder)) {
            $output->writeln('- ' . $fileOrFolder . ' -> file', $output::VERBOSITY_DEBUG);
            yield $fileOrFolder;
        } elseif (is_dir($fileOrFolder)) {
            $output->writeln('- ' . $fileOrFolder . ' -> folder', $output::VERBOSITY_DEBUG);
            foreach ($this->locateFiles($fileOrFolder, $output) as $file) {
                yield $file;
            }
        } else {
            foreach (glob($this->escapeGlobPattern($fileOrFolder), GLOB_BRACE) as $item) {
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
