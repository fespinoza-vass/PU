<?php

namespace WolfSellers\Recategorizar\Console\Command;

use WolfSellers\Recategorizar\Model\Recategorizar  as RecategorizarGral;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class Recategorizar extends Command
{

     protected $_recategorizarGral;


    public function __construct(
        RecategorizarGral $recategorizarGral,
        string $name = null
    ) {
        parent::__construct($name);
        $this->_recategorizarGral=$recategorizarGral;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wolfsellers:categories')
            ->setDescription('Regenerar contenido Categorías')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $output->writeln("Inicia proceso de recategorizar categorias: ".date("Y-m-d H:i:s"));
            $this->_recategorizarGral->execute();
            $output->writeln("Termina proceso de recategorizar categorias: ".date("Y-m-d H:i:s"));
        }
        catch(\Exception $e){
            $output->writeln("Ocurrio un Error favor de revisar el log  => ".$e->getMessage());
            $output->writeln("Termina proceso de recategorizar categorias: ".date("Y-m-d H:i:s"));
        }
    }
}
