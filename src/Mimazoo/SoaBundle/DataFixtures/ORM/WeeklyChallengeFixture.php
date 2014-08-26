<?php

namespace Mimazoo\SoaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mimazoo\SoaBundle\Entity\WeeklyChallenge;

class WeeklyChallengeFixture extends AbstractFixture implements OrderedFixtureInterface 
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        
        $weeklyChallenge = array();
        
        /*$weeklyChallenge['first'] = new WeeklyChallenge();
        $weeklyChallenge['first']->setDescription('COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!');
        $weeklyChallenge['first']->setType(1);

        $weeklyChallenge['second'] = new WeeklyChallenge();
        $weeklyChallenge['second']->setDescription('DEFEAT THE MOST SNAILS ACROSS THE WEEK!');
        $weeklyChallenge['second']->setType(2);*/


        foreach ($weeklyChallenge as $key => $wc) {
            $manager->persist($wc);
            $manager->flush();
        }

    }
    
    public function getOrder()
    {
        return 10; // the order in which fixtures will be loaded
    }
    
}
