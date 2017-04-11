<?php

namespace AppBundle\Command;

use AppBundle\AppBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Controller\HomeController;
use AppBundle\Service\StartCrawlerService;
use Doctrine\ORM\EntityManager;

class CreateCrawlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:start-crawler')
            ->setDescription('Update movies')
            ->setHelp('THis command allows you to update movies from database...')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
           'Start Crawler',
            "============",
            '',
        ]);

        $output->writeln('This will take 5 minutes');
        $startCrawler = $this->getContainer()->get('start_crawler');
        $startCrawler->startCrawlerAction();
    }
}