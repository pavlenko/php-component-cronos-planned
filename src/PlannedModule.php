<?php

namespace PE\Component\Cronos\Planned;

use PE\Component\Cronos\Core\ClientAction;
use PE\Component\Cronos\Core\Module;
use PE\Component\Cronos\Core\QueueInterface;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Planned\Storage\StorageInterface;

class PlannedModule extends Module
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function attachServer(ServerInterface $server): void
    {
        $server->attachListener(ServerInterface::EVENT_ENQUEUE_TASKS, [$this, 'onEnqueueTasks']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_EXECUTED, [$this, 'onTaskExecuted']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this, 'onTaskEstimate']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_PROGRESS, [$this, 'onTaskProgress']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_FINISHED, [$this, 'onTaskFinished']);
        $server->attachListener(ServerInterface::EVENT_CLIENT_ACTION, [$this, 'onClientAction']);
    }

    /**
     * @inheritDoc
     */
    public function detachServer(ServerInterface $server): void
    {
        $server->detachListener(ServerInterface::EVENT_ENQUEUE_TASKS, [$this, 'onEnqueueTasks']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_EXECUTED, [$this, 'onTaskExecuted']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this, 'onTaskEstimate']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_PROGRESS, [$this, 'onTaskProgress']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_FINISHED, [$this, 'onTaskFinished']);
        $server->detachListener(ServerInterface::EVENT_CLIENT_ACTION, [$this, 'onClientAction']);
    }

    /**
     * @internal
     *
     * @param QueueInterface $queue
     */
    public function onEnqueueTasks(QueueInterface $queue): void
    {
        $tasks = $this->storage->getExecutableTasks();

        foreach ($tasks as $task) {
            $queue->enqueue($task->setModuleID($this->getID()));
        }
    }

    /**
     * @internal
     *
     * @param TaskInterface $task
     */
    public function onTaskExecuted(TaskInterface $task): void
    {
        if ($task->getModuleID() === $this->getID()) {
            $this->storage->updateTask($task);
        }
    }

    /**
     * @internal
     *
     * @param TaskInterface $task
     */
    public function onTaskEstimate(TaskInterface $task): void
    {
        if ($task->getModuleID() === $this->getID()) {
            $this->storage->updateTask($task);
        }
    }

    /**
     * @internal
     *
     * @param TaskInterface $task
     */
    public function onTaskProgress(TaskInterface $task): void
    {
        if ($task->getModuleID() === $this->getID()) {
            $this->storage->updateTask($task);
        }
    }

    /**
     * @internal
     *
     * @param TaskInterface $task
     */
    public function onTaskFinished(TaskInterface $task): void
    {
        if ($task->getModuleID() === $this->getID()) {
            $this->storage->updateTask($task);
        }
    }

    /**
     * @internal
     *
     * @param ClientAction $clientAction
     */
    public function onClientAction(ClientAction $clientAction): void
    {
        switch ($clientAction->getName()) {
            case PlannedAPI::INSERT_TASK:
                $clientAction->setResult($this->storage->insertTask($clientAction->getParams()));
                break;
            case PlannedAPI::UPDATE_TASK:
                $clientAction->setResult($this->storage->updateTask($clientAction->getParams()));
                break;
            case PlannedAPI::REMOVE_TASK:
                $clientAction->setResult($this->storage->removeTask($clientAction->getParams()));
                break;
        }
    }
}
