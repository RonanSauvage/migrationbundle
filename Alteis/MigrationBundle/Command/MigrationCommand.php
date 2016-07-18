<?php

namespace Alteis\MigrationBundle\Command;
use Alteis\MigrationBundle\Command\CommonCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use RMA\Bundle\DumpBundle\Command\DumpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  Class Migration Command
 *  Appel la commande dump de rma puis lancer la migration avec migration bundle
 */
class MigrationCommand extends CommonCommand
{

    protected function configure()
    {
        $this->setName('alteis:migration:migrate')
             ->setDescription('Cette commande permet de générer un dump et lancer une migration assurée par le bundle DoctrineMigrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * Récupérer le nom de la base de données de l'application
         */
        $db = $this->getContainer()->getParameter('database_name');

        /**
         * Appel le dump de rma
         */
        $commandRma = $this->getApplication()->find('rma:dump:cron');

        /**
         * On passe la base de données de l'application en argument
         */
        $arguments = array(
            'databases' => array($db)
        );
        $input = new ArrayInput($arguments);

        /**
         * Exécuter la commande rma dump
         */
        $commandRma->run($input,$output);

        /**
         * On prépare le nouvel input pour les commandes de doctrine migration
         */
        $input = new ArrayInput(array());
        /**
         *Appel la commande migrate de migration bundle
         *
         */
        $commandDoctrineMigrate = $this->getApplication()->find('doctrine:migrations:migrate');

        $commandDoctrineMigrate->run($input,$output);

    }
}