<?php
namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;
use Throwable;

class UploadedImage
{
    /**
     * Crops a validated JPG/PNG/WebP upload to a display-ready JPEG.
     * Stores only the new random relative filename; the original is never published.
     */
    public function prepare(UploadedFile $file, string $kind): string
    {
        if (! in_array($kind, ['products', 'avatars'], true)) {
            throw new RuntimeException('Invalid image category.');
        }
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Please select a valid image file.');
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            throw new RuntimeException('Image must not exceed 2 MB.');
        }
        if (! extension_loaded('gd')) {
            throw new RuntimeException('PHP GD extension is disabled. Enable extension=gd in XAMPP php.ini and restart the PHP server.');
        }

        $relativeDir = 'uploads/' . $kind;
        $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeDir);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Could not create the image directory.');
        }

        $relative = $relativeDir . '/' . bin2hex(random_bytes(16)) . '.jpg';
        $destination = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        try {
            // User uploads are temporary files; CI4 Image safely decodes and prepares them.
            service('image')->withFile($file->getTempName())
                ->fit($kind === 'avatars' ? 256 : 640, $kind === 'avatars' ? 256 : 640, 'center')
                ->save($destination, 85);
            if (! is_file($destination)) {
                throw new RuntimeException('Image processing did not create the thumbnail.');
            }
        } catch (Throwable $e) {
            if (is_file($destination)) {
                @unlink($destination);
            }
            log_message('error', 'Image processing failed: ' . $e->getMessage());
            throw new RuntimeException('The uploaded image could not be prepared. Check PHP GD and try a JPG, PNG, or WebP image.');
        }
        return $relative;
    }

    public function remove(?string $relative): void
    {
        if (! $relative || ! preg_match('~^uploads/(?:products|avatars)/[0-9a-f]{32}\.jpg$~', $relative)) {
            return;
        }
        $file = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
