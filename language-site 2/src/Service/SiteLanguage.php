<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class SiteLanguage
{
    public const LANGUAGES = ['ru', 'en'];

    public function __construct(
        #[Autowire('%env(resolve:SITE_LANGUAGE_FILE)%')]
        private readonly string $filePath,
        private readonly Filesystem $filesystem,
    ) {
    }

    public function getLanguage(): string
    {

        if (!is_file($this->filePath)) {
            return 'ru';
        }

        $language = trim($this->filesystem->readFile($this->filePath));
        if (!in_array($language, self::LANGUAGES, true)) {
            throw new \RuntimeException('Неверный язык в настройке сайта, выполните app:change-language ru');
        }

        return $language;
    }

    public function setLanguage(string $language): void
    {
        if (!in_array($language, self::LANGUAGES, true)) {
            throw new \InvalidArgumentException('Доступны только ru и en');
        }


        $this->filesystem->dumpFile($this->filePath, $language."\n");
    }
}
