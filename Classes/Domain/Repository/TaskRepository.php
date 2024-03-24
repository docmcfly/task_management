<?php
declare(strict_types = 1);
namespace Cylancer\TaskManagement\Domain\Repository;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 C.Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Domain\Repository
 */
class TaskRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    public function findOpenTasks()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('doneAt', 0));
        return $query->execute();
    }

    public function findDoneTasks()
    {
        $today = new \DateTime('NOW');
        $before = $today->sub(date_interval_create_from_date_string('14 months'));
        $query = $this->createQuery();
        $query->matching($query->logicalAnd($query->logicalNot($query->equals('doneAt', 0)), $query->greaterThan('doneAt', $before)));
        $query->setOrderings([
            'doneAt' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
        ]);
        return $query->execute();
    }

    public function findRepeatTasks()
    {
        $today = new \DateTime('NOW');
        $today = $today->format('y-m-d');
        $query = $this->createQuery();
        $query->matching( //
        $query->logicalAnd( //
        $query->logicalNot($query->equals('doneAt', 0)), //
        $query->logicalNot($query->equals('nextRepetition', 0)), 
        $query->lessThanOrEqual('nextRepetition', $today)));

        return $query->execute();
    }
}
