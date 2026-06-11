<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FileUploaderService
{
    public function __construct(
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/users')]
        private string $uploadDir,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $safeName = $this->slugger->slug(
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
        );
        $fileName = $safeName . '-' . bin2hex(random_bytes(8)) . '.' . $file->guessExtension();

        $file->move($this->uploadDir, $fileName);

        return $fileName;
    }

    public function remove(string $fileName): void
    {
        $path = $this->uploadDir . '/' . $fileName;

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
