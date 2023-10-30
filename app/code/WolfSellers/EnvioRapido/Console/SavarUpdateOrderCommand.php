<?php
namespace WolfSellers\EnvioRapido\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WolfSellers\EnvioRapido\Helper\SavarHelper;

class SavarUpdateOrderCommand extends Command
{

    /** @var SavarHelper */
    protected $_savarHelper;
    public function __construct(
        SavarHelper $savarHelper,
        string $name = null
    ){
        $this->_savarHelper = $savarHelper;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('wolfsellers:savarupdateorder');
        $this->setDescription('Update Savar Order Status');

        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_savarHelper->updateSavarOrders();
        $output->writeln("Savar Order Update has been finished");
    }
}
