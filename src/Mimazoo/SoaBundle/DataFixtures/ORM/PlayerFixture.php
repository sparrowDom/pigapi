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
		$players['mitja']->setName('Mitja Poljšak');
        $players['mitja']->setSlug('mitja-poljsak');
        $players['mitja']->setSurname('Poljšak');
        $players['mitja']->setFirstName('Mitja');
        $players['mitja']->setDeviceAccessToken('deviceToken1');
        $players['mitja']->setApplePushToken("%6E%E3%BB%33%E5%0A%05%67%FB%56%24%1E%92%4E%A7%55%0D%60%53%B0%FB%66%A6%0E%D7%66%38%2A%07%11%14%26");
		$players['mitja']->setDistanceBest(0);

        $players['domen'] = new Player();
        $players['domen']->setFbId('608899282');
        $players['domen']->setFbAccessToken('CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY');
        $players['domen']->setSlug('domen-grabec');
        $players['domen']->setName('Domen Grabec');
        $players['domen']->setSurname('Grabec');
        $players['domen']->setFirstName('Domen');
        $players['domen']->setIsSuperUser(true);
        $players['domen']->setDeviceAccessToken('deviceToken2');
        $players['domen']->setDistanceBest(0);

        $players['rick'] = new Player();
        $players['rick']->setFbId('100006462098682');
        $players['rick']->setFbAccessToken('CAAFM6NnZBvQoBAE9Y2qydlfTo6pMfoVpDLkglq8gQwZBREnBPXBXMGXgQcXLKTmAv5Fc1cnaI1crFO8qdJy73DnG1ZCImv2aNH0waaKx2Ch4Vihl6DZCTqtoAJEKXFHDYlxZCaJskOwrKUwwbN1z0atmXFkXX1MKRFqM3ZAovg0RBzt3qNurgO');
        $players['rick']->setSlug('rick-ricky');
        $players['rick']->setName('Rick Amfdfbjihfhb Wongsky');
        $players['rick']->setSurname('Wongsly');
        $players['rick']->setFirstName('Rick');
        $players['rick']->setDeviceAccessToken('deviceToken3');
        $players['rick']->setDistanceBest(0);

        $players['joe'] = new Player();
        $players['joe']->setFbId('100006480546572');
        $players['joe']->setFbAccessToken('CAAFM6NnZBvQoBALDUv0CgFoUGbrZBenZCblI7A8W06ab6dmk7sZAZCGvnC0dOtS4hx515HbWUYvvmIzkoZAkxNh5mFJc6ZCQy2xsKvriXKCzxQS0BQW8OP9ISE5mrBWtnzPO2xtmY4ohwIhoIs566w96b6p5XT8GYXxI3plWIXcRbtfuAte50FZC');
        $players['joe']->setSlug('joe-joey');
        $players['joe']->setName('Joe Amfdhjedfegb Letuchysky');
        $players['joe']->setSurname('Letuchysky');
        $players['joe']->setFirstName('Joe');
        $players['joe']->setDistanceBest(0);

        $players['mike'] = new Player();
        $players['mike']->setFbId('100006469538852');
        $players['mike']->setFbAccessToken('CAAFM6NnZBvQoBAEUbLlTduIwrWDpTVStKGTLgZAU2nmXamJrgmaZA3R4H2YCGtvO1QEZAvQyfZAXilnEX6ie1q5lpD56aXWTnW7N5N5WiSpqzf9KDZCZA3hLSEGvZBGhqUBYQFCJEuchs58Mr6LsYbtCrvyOEZBBHfIrR8Rkf6hKgmOqhLfrV8wlhByRCqJWph9wZD');
        $players['mike']->setSlug('mikey-mike');
        $players['mike']->setName('Mike Amfdfiechheb Letuchyescu');
        $players['mike']->setSurname('Letuchyescu');
        $players['mike']->setFirstName('Mike');
        $players['mike']->setDistanceBest(0);

        $players['donna'] = new Player();
        $players['donna']->setFbId('100006480426530');
        $players['donna']->setFbAccessToken('CAAFM6NnZBvQoBAFnyLOrSY66i7bZCu2eUX3SsS9s9X7NzRg0VUt22fx2sZCMYJt4ksSyl7rBmmICbK0WSTHZA0okswOJB4FlKh6sV4ZBnFQqDGRMr7cWNAiiIiK6TqHbXHriwFsUkeLoJ3MHx6qriH12LULvwVKPoUMgPjcCH07mZAcb99nAnvicQuPIzddcIZD');
        $players['donna']->setSlug('tommy-tom');
        $players['donna']->setName('Donna Amfdhjdbfecj Chengescumansteins');
        $players['donna']->setSurname('Chengescumansteins');
        $players['donna']->setFirstName('Donna');
        $players['donna']->setDistanceBest(0);

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
