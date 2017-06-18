<?php
declare(strict_types = 1);

namespace Temirkhan\FlushListenerBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Transaction mechanism listener's tests
 */
class OnResponseFlushListenerTest extends TestCase
{
    /**
     * Doctrine entity manager
     *
     * @var MockObject|EntityManagerInterface
     */
    private $entityManager;

    /**
     * Transaction mechanism listener
     *
     * @var OnResponseFlushListener
     */
    private $responseListener;

    /**
     * Environment preset
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager    = $this->createMock(EntityManagerInterface::class);
        $this->responseListener = new OnResponseFlushListener($this->entityManager);
    }

    /**
     * Environment reset
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager    = null;
        $this->responseListener = null;
    }

    /**
     * Tests transaction prevention
     */
    public function testTransactionRollback()
    {
        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->responseListener->onTransactionRollback();
        $this->responseListener->onTransactionCommit();
    }

    /**
     * Tests transaction commit
     */
    public function testTransactionCommit()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->responseListener->onTransactionCommit();
    }

    /**
     * Tests behavior on non-master kernel response
     */
    public function testOnNonMasterKernelResponse()
    {
        $event = $this->createFilterResponseEvent();

        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(false);

        $event
            ->expects($this->never())
            ->method('getResponse');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->responseListener->onKernelResponse($event);
    }

    /**
     * Tests behavior on master kernel response with valid status code
     *
     * @param int $statusCode
     *
     * @dataProvider validStatusCodesProvider
     */
    public function testOnMasterKernelResponse(int $statusCode)
    {
        $event    = $this->createFilterResponseEvent();
        $response = $this->createResponse();

        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $event
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->responseListener->onKernelResponse($event);
    }

    /**
     * Tests behavior on master kernel response with bad status code
     *
     * @param int $statusCode
     *
     * @dataProvider invalidStatusCodesProvider
     */
    public function testOnBadMasterKernelResponse(int $statusCode)
    {
        $event    = $this->createFilterResponseEvent();
        $response = $this->createResponse();

        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $event
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->responseListener->onKernelResponse($event);
    }

    /**
     * "ok" http status codes provider
     *
     * @return array
     */
    public function invalidStatusCodesProvider(): array
    {
        return [
            [400],
            [403],
            [500],
        ];
    }

    /**
     * Bad http status codes provider
     *
     * @return array
     */
    public function validStatusCodesProvider(): array
    {
        return [
            [100],
            [200],
            [300],
        ];
    }

    /**
     * Creates response filtering event
     *
     * @return MockObject|FilterResponseEvent
     */
    private function createFilterResponseEvent(): FilterResponseEvent
    {
        return $this->createMock(FilterResponseEvent::class);
    }

    /**
     * Creates response
     *
     * @return MockObject|Response
     */
    private function createResponse(): Response
    {
        return $this->createMock(Response::class);
    }
}
