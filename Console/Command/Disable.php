<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Threecommerce\Maintenance\Helper\Data;

class Disable extends Command
{
    protected $helper;

    public function __construct(
        Data   $helper,
        string $name = null
    )
    {
        $this->helper = $helper;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('3com:maintenance:disable')
            ->setDescription('Enable Threecommerce Maintenance Mode: php bin/magento 3com:maintenance:disable');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helper->removeFlag();
        $output->writeln('<info>Maintenance mode disable</info>');
        return Cli::RETURN_SUCCESS;
    }
}
