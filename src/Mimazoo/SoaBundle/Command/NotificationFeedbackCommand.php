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


class NotificationFeedbackCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notifications:feedback')
            ->setDescription('Receive notification feedback')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	
        $feedbackService = $this->getContainer()->get("rms_push_notifications.ios.feedback");
        $uuids = $feedbackService->getDeviceUUIDs();

        echo "Problematic messages: " . count($uuids);
        foreach ($uuids as $uuid) {
            print_r($uuid);
        }
    }
    
}