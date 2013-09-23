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
		$players['mitja']->setFbAccessToken('CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE57');
		$players['mitja']->setApplePushToken('BLABLA123456789BLABLA');
		$players['mitja']->setName('Mitja Poljšak');
        $players['mitja']->setSlug('mitja-poljsak');
        $players['mitja']->setSurname('Poljšak');
        $players['mitja']->setFirstName('Mitja');
		$players['mitja']->setDistanceBest(-1);
        $players['mitja']->setPresentSelected(1);

        $players['domen'] = new Player();
        $players['domen']->setFbId('608899282');
        $players['domen']->setFbAccessToken('CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY');
        $players['domen']->setApplePushToken('BLABLA123456789BLABLA');
        $players['domen']->setSlug('domen-grabec');
        $players['domen']->setName('Domen Grabec');
        $players['domen']->setSurname('Grabec');
        $players['domen']->setFirstName('Domen');
        $players['domen']->setDistanceBest(-1);
        $players['domen']->setPresentSelected(1);

        $players['rick'] = new Player();
        $players['rick']->setFbId('100006462098682');
        $players['rick']->setFbAccessToken('CAAFM6NnZBvQoBAE9Y2qydlfTo6pMfoVpDLkglq8gQwZBREnBPXBXMGXgQcXLKTmAv5Fc1cnaI1crFO8qdJy73DnG1ZCImv2aNH0waaKx2Ch4Vihl6DZCTqtoAJEKXFHDYlxZCaJskOwrKUwwbN1z0atmXFkXX1MKRFqM3ZAovg0RBzt3qNurgO');
        $players['rick']->setApplePushToken('BLABLA123456789BLABLA');
        $players['rick']->setSlug('rick-ricky');
        $players['rick']->setName('Rick Amfdfbjihfhb Wongsky');
        $players['rick']->setSurname('Wongsly');
        $players['rick']->setFirstName('Rick');
        $players['rick']->setDistanceBest(-1);
        $players['rick']->setPresentSelected(1);

        $players['joe'] = new Player();
        $players['joe']->setFbId('100006480546572');
        $players['joe']->setFbAccessToken('CAAFM6NnZBvQoBALDUv0CgFoUGbrZBenZCblI7A8W06ab6dmk7sZAZCGvnC0dOtS4hx515HbWUYvvmIzkoZAkxNh5mFJc6ZCQy2xsKvriXKCzxQS0BQW8OP9ISE5mrBWtnzPO2xtmY4ohwIhoIs566w96b6p5XT8GYXxI3plWIXcRbtfuAte50FZC');
        $players['joe']->setApplePushToken('BLABLA123456789BLABLA');
        $players['joe']->setSlug('joe-joey');
        $players['joe']->setName('Joe Amfdhjedfegb Letuchysky');
        $players['joe']->setSurname('Letuchysky');
        $players['joe']->setFirstName('Joe');
        $players['joe']->setDistanceBest(-1);
        $players['joe']->setPresentSelected(1);

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