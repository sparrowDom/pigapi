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


class MessageTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('message:test')
            ->setDescription('Send a test message to Domen')
            ->addArgument('msg', InputArgument::REQUIRED, 'The message you want to send to Domen')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$msg = $input->getArgument('msg');
        $logMsg = "Sending out message to domen player '$msg'.";
        $this->getContainer()->get('logger')->info($logMsg , get_defined_vars());

        $repository = $this->getContainer()->get("doctrine")
            ->getRepository('MimazooSoaBundle:Player');

        $player = $repository->findOneByName("Domen Grabec");

        $shared = $this->getContainer()->get("mimazoo_soa.shared");
        $notification = $shared->queuePushNotification($player, $msg);
        $shared->sendOneNotification($notification);
        echo "Sending message to Domen: " . $logMsg . PHP_EOL;
    }
}