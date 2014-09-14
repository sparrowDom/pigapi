<?php
namespace Mimazoo\SoaBundle\Service;

use Symfony\Component\Validator\Validator;
use Doctrine\ORM\EntityManager;
use RMS\PushNotificationsBundle\Message\iOSMessage;

use Mimazoo\SoaBundle\Entity\Notification;

class SharedService
{
    protected $em;
    protected $validator;

    public function __construct(EntityManager $em, Validator $validator) {
        $this->em = $em;
        $this->validator = $validator;
    }

    // TODO: increase max execution time, since this might take a while
    // Warning you change this you need to change it in ChangeChallengeCommandAlso.y
    public function sendMessageToAllPlayers($message) {
    	$repository = $this->em
            ->getRepository('MimazooSoaBundle:Player');


        $qb = $repository->createQueryBuilder('p');
        $qb->where('p.applePushToken IS NOT NULL');

        foreach($qb->getQuery()->getResult() as $player){
            $this->queuePushNotification($player, $message);
        }
    }

       	// Only if player has push notification token. Else skip it
    public function queuePushNotification($player, $message){
    	$apnToken = $player->getApplePushToken();
    	if ($apnToken == null || strlen($apnToken) < 5)
    		return false; // Invalid or nonexisting token

        $notification = new Notification();
        $notification->setMessage($message);
        $notification->setApplePushToken($player->getApplePushToken());
        $notification->setPlayer($player);

        $errors = $this->validator->validate($notification);

        if (count($errors) > 0)
            $this->logError("Errors inserting push notification to DB: " . print_r($errors, true));

        $this->em->persist($notification);
        try {
            $this->em->flush();
        } catch (DBALException  $e) {
            $this->logError("Errors flushing push notification to DB: " . $e->getMessage());
        }

    }
}