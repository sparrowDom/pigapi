<?php
namespace Mimazoo\SoaBundle\Service;

use Symfony\Component\Validator\Validator;
use Doctrine\ORM\EntityManager;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use RMS\PushNotificationsBundle\Service\Notifications;
use Mimazoo\SoaBundle\Entity\Notification;
use Symfony\Bridge\Monolog\Logger;

class SharedService
{
    protected $em;
    protected $validator;
    protected $logger;
    protected $pushNotifications;

    public function __construct(EntityManager $em, Validator $validator, Logger $logger, Notifications $pushNotifications) {
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->pushNotifications = $pushNotifications;
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
            return $notification;
        } catch (DBALException  $e) {
            $this->logError("Errors flushing push notification to DB: " . $e->getMessage());
        }

    }

    public function sendOneNotification($notification) {
        if(strlen($notification->getPlayer()->getApplePushToken()) > 5){
            //limit is 100 characters
            $message = new iOSMessage();
            $message->setMessage($notification->getMessage());
            $message->setAPSSound("default");
            $message->setDeviceIdentifier(str_replace('%', '', $notification->getPlayer()->getApplePushToken()));
            $this->pushNotifications->send($message);

            $this->logger->info('Notifying player id: ' . $notification->getPlayer()->getId() . " message: " . $notification->getMessage() , get_defined_vars());
            $notification->setSentOn(new \DateTime("now"));
        }
    }
}