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

        $repository = $this->getContainer()->get("doctrine")
            ->getRepository('MimazooSoaBundle:Player');

        $player = $repository->findOneByName("Domen Grabec");

        $logMsg = "Sending out message to player " . $player->getName() . " '$msg'.";
        $this->getContainer()->get('logger')->info($logMsg , get_defined_vars());

        $shared = $this->getContainer()->get("mimazoo_soa.shared");
        $notification = $shared->queuePushNotification($player, $msg);
        $notification->setSentOn(new \DateTime("now"));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $shared->sendOneNotification($notification);

        $em->persist($notification);
        echo $logMsg . PHP_EOL;
    }
}
