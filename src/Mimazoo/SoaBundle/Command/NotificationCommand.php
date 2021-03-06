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


class NotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notifications:send')
            ->setDescription('Send pending push notifications')
            ->addArgument('batchSize', InputArgument::OPTIONAL, 'How many do you want to send in one batch?')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$batchSize = $input->getArgument('batchSize');
        $this->getContainer()->get('logger')->info("Attempting to send out $batchSize notifications" , get_defined_vars());

        $em = $this->getContainer()->get('doctrine')->getManager();

        $repository = $this->getContainer()->get('doctrine')
             ->getRepository('MimazooSoaBundle:Notification');

        $qb = $repository->createQueryBuilder('n');
        $qb->where('n.sentOn IS NULL')
           ->orderBy('n.id', 'ASC')
           ->setMaxResults($batchSize);

        $shared = $this->getContainer()->get("mimazoo_soa.shared");
        $result = $qb->getQuery()->getResult();
        foreach ($result as $notification) {
            $shared->sendOneNotification($notification);
            echo "Sending out message: " . $notification->getMessage() .  PHP_EOL;
        }

        // Flush the changes to db
        $em->flush($result);

        return false;
    }
    
}