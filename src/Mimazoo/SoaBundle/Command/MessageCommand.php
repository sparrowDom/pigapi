<?php

namespace Mimazoo\SoaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Mimazoo\SoaBundle\Entity\User;
use Mimazoo\SoaBundle\ValueObject\TimeRange;
use Mimazoo\SoaBundle\ValueObject\TimeRangeCommand;
use Mimazoo\SoaBundle\ValueObject\DailyDateTimeInterval;
use Mimazoo\SoaBundle\ValueObject\YearlyDateTimeInterval;
use RMS\PushNotificationsBundle\Message\iOSMessage;

use Mimazoo\SoaBundle\Model\Schedule\Schedule;


class MessageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('message:new')
            ->setDescription('Send a new message to players')
            ->addArgument('msg', InputArgument::REQUIRED, 'The message you want to send to players')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$msg = $input->getArgument('msg');
        $logMsg = "Sending out message to all players '$msg'.";
        $this->getContainer()->get('logger')->info($logMsg , get_defined_vars());

        $shared = $this->getContainer()->get("mimazoo_soa.shared");

        $shared->sendMessageToAllPlayers($msg);

        echo $logMsg . PHP_EOL;
    }
}