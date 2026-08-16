<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, string $targetDirectory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw new FileException('Error during file upload: '.$e->getMessage());
        }

        return $fileName;
    }

    public function remove(string $targetDirectory, ?string $fileName): void
    {
        if ($fileName) {
            $filePath = $targetDirectory.'/'.$fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
