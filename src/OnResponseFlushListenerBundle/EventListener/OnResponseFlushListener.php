<?php

declare(strict_types = 1);

namespace Temirkhan\OnResponseFlushListenerBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Transaction mechanism
 */
class OnResponseFlushListener
{
    /**
     * Doctrine entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Flag that prevents flushing(committing)
     *
     * @var bool
     */
    private $preventCommit = false;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Sets flag to prevent flushing for some reason
     */
    public function onTransactionRollback()
    {
        $this->preventCommit = true;
    }

    /**
     * Flushes all changes if there was no prevention before
     */
    public function onTransactionCommit()
    {
        if(!$this->preventCommit){
            $this->entityManager->flush();
        }
    }

    /**
     * Flushes all changes if there was hernel response with status code lower than 400
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();

        if($response->getStatusCode() < 400){
            $this->onTransactionCommit();
        }
    }
}
