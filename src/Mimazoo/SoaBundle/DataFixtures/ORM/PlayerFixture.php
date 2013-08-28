<?php

namespace Mimazoo\SoaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mimazoo\SoaBundle\Entity\Player;

class PlayerFixture extends AbstractFixture implements OrderedFixtureInterface 
{
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager) {
		
		$players = array();
		
		$players['mitja'] = new Player();
		$players['mitja']->setFbId('620310212');
		$players['mitja']->setFbAccessToken('whatever');
		$players['mitja']->setApplePushToken('BLABLA123456789BLABLA');
		$players['mitja']->setName('Mitja Poljšak');
        $players['mitja']->setSlug('mitja-poljsak');
        $players['mitja']->setSurname('Poljšak');
        $players['mitja']->setFirstName('Mitja');
		$players['mitja']->setDistanceBest(-1);
        $players['mitja']->setPresentSelected(1);

        $players['domen'] = new Player();
        $players['domen']->setFbId('608899282');
        $players['domen']->setFbAccessToken('CAAFM6NnZBvQoBAMt4AOS033S1NMRQmlUt67W9P8Ri4iI9WB6kR6aGZA4ZCC6tMkZAwdZCqcPYfJOgSZANxZA4PJCkNgp94FYmLIBw1ABr6vMC1iFFzs1tH349XyPxzj5tP7r3M5H8mmDUUIXk4b2H8n');
        $players['domen']->setApplePushToken('BLABLA123456789BLABLA');
        $players['domen']->setSlug('domen-grabec');
        $players['domen']->setName('Domen Grabec');
        $players['domen']->setSurname('Grabec');
        $players['domen']->setFirstName('Domen');
        $players['domen']->setDistanceBest(-1);
        $players['domen']->setPresentSelected(1);

		foreach ($players as $key => $player) {
			$manager->persist($player);
			$manager->flush();
			//$this->addReference('player_' . $key, $player);
		}

        $players['domen']->addFriend($players['mitja']);
        $players['mitja']->addFriend($players['domen']);

        foreach ($players as $key => $player) {
            $manager->persist($player);
            $manager->flush();
            //$this->addReference('player_' . $key, $player);
        }

	}
	
	public function getOrder()
	{
		return 10; // the order in which fixtures will be loaded
	}
	
}