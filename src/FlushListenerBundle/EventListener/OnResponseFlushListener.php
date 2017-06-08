<?php

declare(strict_types = 1);

namespace Temirkhan\FlushListenerBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Transaction mechanism
 */
class OnResponseFlushListener
{
    /**
     * Entity manager
     *
     * @var EntityManagerInterface
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
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Prevents flushing
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
        if (!$this->preventCommit) {
            $this->entityManager->flush();
        }
    }

    /**
     * Flushes entity manager on kernel response with status code lower than 400
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();

        if ($response->getStatusCode() < 400) {
            $this->onTransactionCommit();
        }
    }
}
