<?php

namespace BlogBundle\BlogBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BlogBundle\BlogBundle\Service\ParserService;

class ParseCommand extends Command
{
    protected $parser;

    public function __construct(ParserService $parser)
    {
        parent::__construct();
        $this->parser = $parser;
    }

    protected function configure()
    {
        $this->setName('parse:run-parse')
            ->setDescription('Add new articles.')
            ->setHelp('This command allows you to add new articles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
     $this->parser->test();
     $output->writeln('Success test');
    }
}