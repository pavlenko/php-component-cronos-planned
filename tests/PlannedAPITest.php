<?php

namespace PE\Component\Cronos\Planned\Tests;

use PE\Component\Cronos\Core\ClientInterface;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Planned\PlannedAPI;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PlannedAPITest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var PlannedAPI
     */
    private $api;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->api    = new PlannedAPI($this->client);
    }

    public function testInsertTask(): void
    {
        /* @var $task1 TaskInterface|MockObject */
        $task1 = $this->createMock(TaskInterface::class);
        $task2 = clone $task1;

        $this->client
            ->expects(static::once())
            ->method('request')
            ->with(PlannedAPI::INSERT_TASK, $task1)
            ->willReturn($task2);

        self::assertSame($task2, $this->api->insertTask($task1));
    }

    public function testUpdateTask(): void
    {
        /* @var $task1 TaskInterface|MockObject */
        $task1 = $this->createMock(TaskInterface::class);
        $task2 = clone $task1;

        $this->client
            ->expects(static::once())
            ->method('request')
            ->with(PlannedAPI::UPDATE_TASK, $task1)
            ->willReturn($task2);

        self::assertSame($task2, $this->api->updateTask($task1));
    }

    public function testRemoveTask(): void
    {
        /* @var $task1 TaskInterface|MockObject */
        $task1 = $this->createMock(TaskInterface::class);
        $task2 = clone $task1;

        $this->client
            ->expects(static::once())
            ->method('request')
            ->with(PlannedAPI::REMOVE_TASK, $task1)
            ->willReturn($task2);

        self::assertSame($task2, $this->api->removeTask($task1));
    }
}
