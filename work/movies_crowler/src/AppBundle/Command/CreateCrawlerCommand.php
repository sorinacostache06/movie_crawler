<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCrawlerCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('start:crawler')
            ->setDescription('Get all links')
            ->setHelp('THis command insert all links in database...')
            ->addArgument('site', InputArgument::REQUIRED, 'The site after which you extract links ')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
           'Start Crawler',
            "============",
            '',
        ]);

        $output->writeln('This will take a few minutes');
        $startCrawler = $this->getContainer()->get('start_crawler');
        $siteUrl = $input->getArgument('site');
        $startCrawler->startCrawlerAction($siteUrl);
    }
}
