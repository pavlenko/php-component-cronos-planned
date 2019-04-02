<?php

namespace PE\Component\Cronos\Planned\Tests;

use PE\Component\Cronos\Core\ClientAction;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Planned\PlannedAPI;
use PE\Component\Cronos\Planned\PlannedModule;
use PE\Component\Cronos\Planned\Storage\StorageInterface;
use PE\Component\Cronos\Core\QueueInterface;
use PE\Component\Cronos\Core\ServerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PlannedModuleTest extends TestCase
{
    /**
     * @var StorageInterface|MockObject
     */
    private $storage;

    /**
     * @var PlannedModule
     */
    private $module;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);

        $this->module = new PlannedModule($this->storage);
        $this->module->setID('PLANNED');
    }

    public function testAttachServer(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $server->expects(static::exactly(6))->method('attachListener')->withConsecutive(
            [ServerInterface::EVENT_ENQUEUE_TASKS, [$this->module, 'onEnqueueTasks']],
            [ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->module, 'onTaskExecuted']],
            [ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->module, 'onTaskEstimate']],
            [ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->module, 'onTaskProgress']],
            [ServerInterface::EVENT_SET_TASK_FINISHED, [$this->module, 'onTaskFinished']],
            [ServerInterface::EVENT_CLIENT_ACTION, [$this->module, 'onClientAction']]
        );

        $this->module->attachServer($server);
    }

    public function testDetachServer(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $server->expects(static::exactly(6))->method('detachListener')->withConsecutive(
            [ServerInterface::EVENT_ENQUEUE_TASKS, [$this->module, 'onEnqueueTasks']],
            [ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->module, 'onTaskExecuted']],
            [ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->module, 'onTaskEstimate']],
            [ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->module, 'onTaskProgress']],
            [ServerInterface::EVENT_SET_TASK_FINISHED, [$this->module, 'onTaskFinished']],
            [ServerInterface::EVENT_CLIENT_ACTION, [$this->module, 'onClientAction']]
        );

        $this->module->detachServer($server);
    }

    public function testOnEnqueueTasks(): void
    {
        /* @var $task1 TaskInterface|MockObject */
        $task1 = $this->createMock(TaskInterface::class);
        $task1->expects(static::once())->method('setModuleID')->with('PLANNED');

        /* @var $task2 TaskInterface|MockObject */
        $task2 = $this->createMock(TaskInterface::class);
        $task2->expects(static::once())->method('setModuleID')->with('PLANNED');

        $this->storage->expects(static::once())->method('getExecutableTasks')->willReturn([$task1, $task2]);

        /* @var $queue QueueInterface|MockObject */
        $queue = $this->createMock(QueueInterface::class);
        $queue->expects(static::exactly(2))->method('enqueue')->withConsecutive($task1, $task2);

        $this->module->onEnqueueTasks($queue);
    }

    public function testOnTaskExecutedOtherModule(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED2');

        $this->storage->expects(static::never())->method('updateTask');

        $this->module->onTaskExecuted($task);
    }

    public function testOnTaskExecuted(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED');

        $this->storage->expects(static::once())->method('updateTask')->with($task);

        $this->module->onTaskExecuted($task);
    }

    public function testOnTaskEstimateOtherModule(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED2');

        $this->storage->expects(static::never())->method('updateTask');

        $this->module->onTaskEstimate($task);
    }

    public function testOnTaskEstimate(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED');

        $this->storage->expects(static::once())->method('updateTask')->with($task);

        $this->module->onTaskEstimate($task);
    }

    public function testOnTaskProgressOtherModule(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED2');

        $this->storage->expects(static::never())->method('updateTask');

        $this->module->onTaskProgress($task);
    }

    public function testOnTaskProgress(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED');

        $this->storage->expects(static::once())->method('updateTask')->with($task);

        $this->module->onTaskProgress($task);
    }

    public function testOnTaskFinishedOtherModule(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED2');

        $this->storage->expects(static::never())->method('updateTask');

        $this->module->onTaskFinished($task);
    }

    public function testOnTaskFinished(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getModuleID')->willReturn('PLANNED');

        $this->storage->expects(static::once())->method('updateTask')->with($task);

        $this->module->onTaskFinished($task);
    }

    public function testOnClientRequest(): void
    {
        /* @var $task1 TaskInterface|MockObject */
        $task1 = $this->createMock(TaskInterface::class);
        $task2 = clone $task1;

        $this->storage
            ->expects(static::once())
            ->method('insertTask')
            ->with($task1)
            ->willReturn($task2);

        $this->storage
            ->expects(static::once())
            ->method('updateTask')
            ->with($task1)
            ->willReturn($task2);

        $this->storage
            ->expects(static::once())
            ->method('removeTask')
            ->with($task1)
            ->willReturn($task2);

        $this->module->onClientAction($event = new ClientAction(PlannedAPI::INSERT_TASK, $task1));

        static::assertSame($task2, $event->getResult());

        $this->module->onClientAction($event = new ClientAction(PlannedAPI::UPDATE_TASK, $task1));

        static::assertSame($task2, $event->getResult());

        $this->module->onClientAction($event = new ClientAction(PlannedAPI::REMOVE_TASK, $task1));

        static::assertSame($task2, $event->getResult());
    }
}
