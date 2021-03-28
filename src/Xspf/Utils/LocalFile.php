<?php

declare(strict_types=1);

namespace Xspf\Utils;

use Closure;
use Exception;
use Xspf\File\File;

class LocalFile
{
    private string $originalPath;

    private ?string $confirmedPath = null;

    private ?string $absolutePath = null;

    private array $fileMetaData = [];

    public function __construct(string $file)
    {
        $this->originalPath = $file;
        $this->reset();
    }

    public function reset(): void
    {
        $this->fileMetaData = [];

        // Determine confirmed path
        $this->confirmedPath = null;
        if (file_exists($this->originalPath)) {
            $this->confirmedPath = $this->originalPath;
        }

        // Determine absolute path
        if (
            $this->absolutePath === null
            && $this->confirmedPath !== null
            && ($path = realpath($this->confirmedPath)) !== false
            && file_exists($path)
        ) {
            $this->absolutePath = $path;
        }
    }

    private function fromMetadata(string $key, Closure $callback)
    {
        return (!array_key_exists($key, $this->fileMetaData))
            ? $this->fileMetaData[$key] = $callback()
            : $this->fileMetaData[$key];
    }

    public function toXspfFile(bool $disableSanitizing = false): File
    {
        return new File($this->originalPath, $disableSanitizing);
    }

    public function validate(): void
    {
        if (!$this->exists()) {
            throw new Exception('File "' . $this->basename() . '" could not be found!');
        } elseif (!is_writable($this->path())) {
            throw new Exception('File "' . $this->basename() . '" is not writable!');
        } elseif (!is_readable($this->path())) {
            throw new Exception('File "' . $this->basename() . '" is not readable!');
        }
    }

    public function exists(): bool
    {
        return $this->fromMetadata('exists', function () {
            return $this->confirmedPath !== null && file_exists($this->confirmedPath);
        });
    }

    public function size(): ?int
    {
        return $this->fromMetadata('size', function () {
            $size = ($this->exists()) ? filesize($this->confirmedPath) : null;
            if ($size === false) {
                $size = null;
            }

            return $size;
        });
    }

    public function sizeReadable(): string
    {
        return (($size = $this->size()) !== null)
            ? BytesFormatter::formatBytes($size)
            : '? MB';
    }

    public function mtime(): ?int
    {
        return $this->fromMetadata('mtime', function () {
            $mtime = ($this->exists()) ? filemtime($this->confirmedPath) : null;

            if ($mtime === false) {
                $mtime = null;
            }

            return $mtime;
        });
    }

    public function mtimeReadable(): string
    {
        return (($mtime = $this->mtime()) !== null)
            ? date('Y-m-d', $mtime)
            : '?';
    }

    public function delete(): bool
    {
        $result = false;
        if ($this->exists()) {
            $result = unlink($this->confirmedPath);

            $this->reset();
        }

        return $result;
    }

    /**
     * @param mixed $content
     *
     * @return bool
     */
    public function put($content): bool
    {
        if (file_put_contents($this->originalPath, $content) !== false) {
            $this->reset();

            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function read(): ?string
    {
        if ($this->exists() && ($result = file_get_contents($this->confirmedPath)) !== false) {
            return $result;
        }

        return null;
    }

    public function basename(): string
    {
        return $this->fromMetadata('basename', function () {
            return basename($this->path());
        });
    }

    public function touch(): bool
    {
        if (touch($this->path())) {
            $this->reset();

            return true;
        }

        return false;
    }

    public function force(): bool
    {
        if ($this->absolutePath === null) {
            $this->touch();
            $this->delete();
        }

        return $this->absolutePath !== null;
    }

    public function __toString(): string
    {
        return $this->path();
    }

    public function path(): string
    {
        return $this->fromMetadata('path', function () {
            if ($this->absolutePath !== null) {
                return $this->absolutePath;
            }

            if ($this->confirmedPath !== null) {
                return $this->confirmedPath;
            }

            return $this->originalPath;
        });
    }
}
