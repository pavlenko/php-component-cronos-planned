<?php

namespace PE\Component\Cronos\Planned\Storage;

use PE\Component\Cronos\Core\TaskInterface;

interface StorageInterface
{
    /**
     * @return TaskInterface[]
     */
    public function getExecutableTasks(): array;

    /**
     * @param TaskInterface $task
     *
     * @return TaskInterface
     */
    public function insertTask(TaskInterface $task): TaskInterface;

    /**
     * @param TaskInterface $task
     *
     * @return TaskInterface
     */
    public function updateTask(TaskInterface $task): TaskInterface;

    /**
     * @param TaskInterface $task
     *
     * @return TaskInterface
     */
    public function removeTask(TaskInterface $task): TaskInterface;
}