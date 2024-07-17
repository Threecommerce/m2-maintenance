<?php
declare(strict_types=1);

namespace Threecommerce\Maintenance\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Threecommerce\Maintenance\Helper\Data;

class Enable extends Command
{
    const VARIABLE = 'ip';
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
        $this->setName('3com:maintenance:enable')
            ->setDescription('Enable Threecommerce Maintenance Mode: php bin/magento 3com:maintenance:enable --ip xxx.xxx.xxx.xxx,nnn.nnn.nnn.nnn');
        parent::configure();
        $this->addOption(
            self::VARIABLE,
            null,
            InputOption::VALUE_OPTIONAL,
            self::VARIABLE
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helper->setEnable();
        $this->helper->addFlag();
        $listIp = $input->getOption(self::VARIABLE);
        if ($listIp) {
            $listIp = explode(',', $listIp);
            foreach ($listIp as $ip) {
                $this->helper->setExcludeIp($ip);
            }
        }
        $output->writeln('<info>Maintenance mode enabled</info>');
        return Cli::RETURN_SUCCESS;
    }

}
