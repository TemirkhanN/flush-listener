<?php
declare(strict_types = 1);

namespace Temirkhan\OnResponseFlushListenerBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class OnResponseFlushListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Флаг для упреждения коммитов
     *
     * @var bool
     */
    private $preventCommit = false;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Упреждает коммиты через слушатель любыми путями
     */
    public function onTransactionRollback()
    {
        $this->preventCommit = true;
    }

    /**
     * Проводит коммит накопившихся запросов
     */
    public function onTransactionCommit()
    {
        if(!$this->preventCommit){
            $this->entityManager->flush();
        }
    }

    /**
     * Проводит коммит транзакции, если код ответа ниже 400
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
