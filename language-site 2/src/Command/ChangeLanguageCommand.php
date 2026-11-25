<?php

namespace App\Command;

use App\Service\SiteLanguage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[AsCommand(name: 'app:change-language', description: 'Меняет язык сайта для всех пользователей')]
class ChangeLanguageCommand extends Command
{
    public function __construct(private readonly SiteLanguage $siteLanguage)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('language', InputArgument::REQUIRED, 'Язык сайта: ru или en')
            ->setHelp('Пример: php bin/console app:change-language en');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $language = $input->getArgument('language');
        if (!in_array($language, SiteLanguage::LANGUAGES, true)) {
            $output->writeln('<error>Укажите ru или en</error>');

            return Command::INVALID;
        }

        try {
            $this->siteLanguage->setLanguage($language);
        } catch (IOExceptionInterface $exception) {
            $output->writeln('<error>Не удалось сохранить язык, проверьте права на папку var</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Язык сайта: '.$language.'</info>');
        $output->writeln('Обновите страницы в браузере');

        return Command::SUCCESS;
    }
}
