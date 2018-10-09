<?php

namespace App\EventListener;

use App\Entity\SafeboxItem;
use App\Entity\Token;
use App\Model\EncryptUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;

class DatabaseEncryptSubscriber implements EventSubscriber
{
    /** @var  EncryptUtils */
    private $encryptUtils;

    public function __construct(EncryptUtils $encryptUtils)
    {
        $this->encryptUtils = $encryptUtils;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postLoad',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->encrypt($args->getObject());
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->encrypt($args->getObject());
    }

    private function encrypt($entity)
    {
        if ($entity instanceof Token) {
            $entity->setExpirationTimeEncrypted(
                $this->encryptUtils->encrypt($entity->getExpirationTime()->getTimestamp())
            );
        } else if ($entity instanceof SafeboxItem) {
            $entity->setContentEncrypted($this->encryptUtils->encrypt($entity->getContent()));
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Token) {
            $expirationDate = new \DateTime();
            $expirationDate->setTimestamp($this->encryptUtils->decrypt($entity->getExpirationTimeEncrypted()));
            $entity->setExpirationTime($expirationDate);
        } else if ($entity instanceof SafeboxItem) {
            $entity->setContent($this->encryptUtils->decrypt($entity->getContentEncrypted()));
        }
    }
}