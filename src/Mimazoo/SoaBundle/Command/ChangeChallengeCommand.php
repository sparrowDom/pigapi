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

use Mimazoo\SoaBundle\Model\Schedule\Schedule;


class ChangeChallengeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('weeklyChallenge:change')
            ->setDescription('Change to a new weekly challenge')
            ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->info("Completing the old challenge and starting a new one" , get_defined_vars());
        $wcService = $this->getContainer()->get("mimazoo_soa.weekly_challenge");

        $output->write($wcService->completeChallenge());
    }

}