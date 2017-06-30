<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetMoviesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('insert:movies')
            ->setDescription('Find movies in crawler links and insert movie details in database')
            ->setHelp('This command allows you insert the movies find by crawler in database...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Filter movie links',
            "============",
            '',
        ]);

        $output->writeln('This will take a few minutes');
        $insertMovies = $this->getContainer()->get('insert_movies');
        $insertMovies->matchMovies();
    }
}
