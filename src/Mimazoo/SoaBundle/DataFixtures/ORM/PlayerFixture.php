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
		$players['mitja']->setFbAccessToken('CAACEdEose0cBABzQcWYmKRhOUI26qalQQ7R4ijLrnpB5ZAv8LdpJ5a2ElWXraoAywn5KRJLD4N6eaHwpmWzou0ZCvpoN0HauCwJUA8QsXIsjlH56ZABJA3nT5Rn4o58Ja60QheHq54m9dnuW3QykWEkBn5ajTrhZBUDbGYVSTgZDZD');
		$players['mitja']->setApplePushToken('BLABLA123456789BLABLA');
		$players['mitja']->setName('Mitja Poljšak');
		$players['mitja']->setDistanceBest(1000);
		$players['mitja']->setChallengesCounter(15);
		
		foreach ($players as $key => $player) {
			$manager->persist($player);
			$manager->flush();
			$this->addReference('player_' . $key, $player);
		}
		
	}
	
	public function getOrder()
	{
		return 10; // the order in which fixtures will be loaded
	}
	
}