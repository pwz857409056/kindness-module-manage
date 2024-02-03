<?php

namespace Kindness\ModuleManage\Commands;

use Exception;
use Kindness\ModuleManage\Concerns\Console\InteractsWithIO;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Command extends \Symfony\Component\Console\Command\Command
{
    use InteractsWithIO;

    /**
     * The SymfonyStyle implementation.
     *
     * @var SymfonyStyle
     */
    public $symfony;

    /**
     * The input interface implementation.
     *
     * @var InputInterface
     */
    public $input;

    /**
     * The output interface implementation.
     *
     * @var OutputInterface
     */
    public $output;

    /**
     * Run the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception|ExceptionInterface
     */
    public function run(InputInterface $input, OutputInterface $output):int
    {
        $this->input = $input;
        $this->output = $output;
        $this->symfony = new SymfonyStyle($input, $output);
        try {
            $statusCode = parent::run($input, $output);
        } catch (ExceptionInterface $e) {
            throw $e;
        }
        return $statusCode;
    }

    /**
     * Overwrite execute method.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return self::SUCCESS;
    }
}
