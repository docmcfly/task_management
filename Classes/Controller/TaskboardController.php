<?php
namespace Cylancer\CyTaskManagement\Controller;

use Cylancer\CyTaskManagement\Domain\Model\Task;
use Cylancer\CyTaskManagement\Domain\Model\CreationTask;
use Cylancer\CyTaskManagement\Domain\Repository\TaskRepository;
use Cylancer\CyTaskManagement\Service\FrontendUserService;
use Psr\Http\Message\ResponseInterface;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 * 
 */
class TaskboardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    private int $now;

    /**
     * 
     * @param FrontendUserService $frontendUserService
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        private readonly FrontendUserService $frontendUserService,
        private readonly TaskRepository $taskRepository
    ) {
        $this->now = time();
    }

    public function showAction(): ResponseInterface
    {
        $doneTasks = [];
        foreach ($this->taskRepository->findDoneTasks() as $task) {
            $doneTasks[$task->getDoneAt()->format('m')][] = $task;
        }
        $this->view->assign('openTasks', $this->taskRepository->findOpenTasks());
        $this->view->assign('doneTasks', $doneTasks);
        $this->view->assign('newTask', new CreationTask());
        $this->view->assign('fullRenderType', $this->settings['renderType'] == 'full');
        return $this->htmlResponse();
    }

    private function createNow(): \DateTime
    {
        $return = new \DateTime();
        $return->setTimestamp($this->now);
        return $return;
    }

    public function doneAction(Task $task): ResponseInterface
    {
        /** @var Task $task */
        $task = $this->taskRepository->findByUid($task->getUid());
        if ($task->getDoneAt() == null) {
            $task->setDoneAt($this->createNow());
            $task->setUser($this->frontendUserService->getCurrentUser());
            if ($task->getRepeatPeriodCount() > 0) {
                $task->setNextRepetition($this->createNow()
                    ->add(\DateInterval::createFromDateString($task->getRepeatPeriodCount() . ' ' . $task->getRepeatPeriodUnit())));
            }
            $this->taskRepository->update($task);

        }
        return $this->redirect("show");
    }

    public function createAction(CreationTask $newTask): ResponseInterface
    {
        if (strlen($newTask->getTitle()) > 0) {
            /** @var Task $task */
            $task = new Task();
            $task->setTitle($newTask->getTitle());
            $task->setDoneAt(null);
            $task->setUser($this->frontendUserService->getCurrentUser());
            if (!$newTask->getUseRepetition()) {
                $task->setRepeatPeriodCount(0);
                $task->setRepeatPeriodUnit('');
            } else {
                $task->setRepeatPeriodCount($newTask->getRepeatPeriodCount());
                $task->setRepeatPeriodUnit($newTask->getRepeatPeriodUnit());
            }
            if ($task->getRepeatPeriodCount() == 0 || ($task->getRepeatPeriodCount() > 0 && in_array($task->getRepeatPeriodUnit(), Task::REPEAT_PERIOD_UNITS))) {
                $this->taskRepository->add($task);
            }
        }
        return $this->redirect("show");
    }

    public function removeAction(Task $task): ResponseInterface
    {
        if ($task->getDoneAt() == null) {
            $this->taskRepository->remove($task);
        }
        return $this->redirect("show");
    }

    public function duplicateAction(Task $task): ResponseInterface
    {
        /** @var CreationTask $newTask */
        $newTask = new CreationTask();
        $newTask->setTitle($task->getTitle());
        if ($task->getRepeatPeriodCount() > 0) {
            $newTask->setUseRepetition(true);
            $newTask->setRepeatPeriodCount($task->getRepeatPeriodCount());
            $newTask->setRepeatPeriodUnit($task->getRepeatPeriodUnit());
        }
        return $this->createAction($newTask);
    }
}