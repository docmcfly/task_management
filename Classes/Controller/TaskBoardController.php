<?php
namespace Cylancer\TaskManagement\Controller;

use Cylancer\TaskManagement\Domain\Model\Task;
use Cylancer\TaskManagement\Domain\Model\CreationTask;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Cylancer\TaskManagement\Domain\Repository\TaskRepository;
use Cylancer\TaskManagement\Service\FrontendUserService;

// use Cylancer\TaskManagement\Domain\Model\Task;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2020 C. Gogolin <service@cylancer.net>
 * C. Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Controller
 */
class TaskBoardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /** @var FrontendUserService */
    private $frontendUserService = null;

    /** @var TaskRepository     */
    private $taskRepository = null;

    /** @var int  */
    private $now;

    /**
     * 
     * @param FrontendUserService $frontendUserService
     * @param TaskRepository $taskRepository
     */
    public function __construct(FrontendUserService $frontendUserService, TaskRepository $taskRepository)
    {
        $this->frontendUserService = $frontendUserService;
        $this->taskRepository = $taskRepository;
        $this->now = time();
    }

    public function showAction(): void
    {
        $doneTasks = array();
        foreach ($this->taskRepository->findDoneTasks() as $task) {
            $doneTasks[$task->getDoneAt()->format('m')][] = $task;
        }
        $this->view->assign('openTasks', $this->taskRepository->findOpenTasks());
        $this->view->assign('doneTasks', $doneTasks);
        $this->view->assign('newTask', new CreationTask());
        $this->view->assign('fullRenderType', $this->settings['renderType'] == 'full');
    }

    /**
     *
     * @var \DateTime $return
     */
    private function createNow(): \DateTime
    {
        $return = new \DateTime();
        $return->setTimestamp($this->now);
        return $return;
    }

    /**
     *
     * @param Task $currentUserMessage
     */
    public function doneAction(Task $task): void
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
        $this->redirect("show");
    }

    /**
     *
     * @param CreationTask $newTask
     * @param Task $newTask
     */
    public function createAction(CreationTask $newTask): void
    {
        if (strlen($newTask->getTitle()) > 0) {
            /** @var Task $task */
            $task = new Task();
            $task->setTitle($newTask->getTitle());
            $task->setDoneAt(null);
            $task->setUser($this->frontendUserService->getCurrentUser());
            if (! $newTask->getUseRepetition()) {
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
        $this->redirect("show");
    }

    /**
     *
     * @param Task $task
     */
    public function removeAction(Task $task): void
    {
        if ($task->getDoneAt() == null) {
            $this->taskRepository->remove($task);
        }
        $this->redirect("show");
    }

    /**
     *
     * @param Task $task
     */
    public function duplicateAction(Task $task): void
    {
        /** @var CreationTask $newTask */
        $newTask = new CreationTask();
        $newTask->setTitle($task->getTitle());
        if ($task->getRepeatPeriodCount() > 0) {
            $newTask->setUseRepetition(true);
            $newTask->setRepeatPeriodCount($task->getRepeatPeriodCount());
            $newTask->setRepeatPeriodUnit($task->getRepeatPeriodUnit());
        }
        $this->createAction($newTask);
    }
}