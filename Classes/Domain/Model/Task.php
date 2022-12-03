<?php
declare(strict_types = 1);
namespace Cylancer\TaskManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This file is part of the "Task management" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Domain\Model
 */
class Task extends AbstractEntity
{

    const REPEAT_PERIOD_UNITS = [
        'days',
        'weeks',
        'months',
        'years'
    ];

    /** @var string */
    protected $title = '';

    /** @var \DateTime */
    protected $doneAt = null;

    /** @var FrontendUser */
    protected $user = null;

    /** @var integer */
    protected $repeatPeriodCount = 1;

    /** @var string  */
    protected $repeatPeriodUnit = 'weeks';

    /** @var \DateTime  */
    protected $nextRepetition = null;

    /**
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     *
     * @return \DateTime $doneAt
     */
    public function getDoneAt(): ?\DateTime
    {
        return $this->doneAt;
    }

    /**
     *
     * @param \DateTime $doneAt
     * @return void
     */
    public function setDoneAt(?\DateTime $doneAt): void
    {
        $this->doneAt = $doneAt;
    }

    /**
     *
     * @return Integer
     */
    public function getCruserId(): int
    {
        return $this->cruserId;
    }

    /**
     *
     * @param Integer $cruserId
     */
    public function setCruserId(int $cruserId): void
    {
        $this->cruserId = $cruserId;
    }

    /**
     *
     * @param FrontendUser $user
     * @return void
     */
    public function setUser(FrontendUser $user): void
    {
        $this->user = $user;
    }

    /**
     *
     * @return FrontendUser
     */
    public function getUser(): ?FrontendUser
    {
        return $this->user;
    }

    /**
     *
     * @return integer
     */
    public function getRepeatPeriodCount(): int
    {
        return $this->repeatPeriodCount;
    }

    /**
     *
     * @param integer $repeatPeriodCount
     */
    public function setRepeatPeriodCount(int $repeatPeriodCount): void
    {
        $this->repeatPeriodCount = $repeatPeriodCount;
    }

    /**
     *
     * @return string
     */
    public function getRepeatPeriodUnit(): string
    {
        return $this->repeatPeriodUnit;
    }

    /**
     *
     * @return string
     */
    public function getTranslatedRepeatPeriodUnit(): string
    {
        return LocalizationUtility::translate('taskManagement.form.openTasks.repeatPeriodUnit.' . ($this->repeatPeriodCount == 1 ? 'singular.' : 'plural.') . $this->repeatPeriodUnit, 'task_management');
    }

    /**
     *
     * @param string $repeatPeriodUnit
     */
    public function setRepeatPeriodUnit(string $repeatPeriodUnit): void
    {
        $this->repeatPeriodUnit = $repeatPeriodUnit;
    }

    /**
     *
     * @return \DateTime
     */
    public function getNextRepetition(): ?\DateTime
    {
        return $this->nextRepetition;
    }

    /**
     *
     * @param \DateTime $nextRepetition
     */
    public function setNextRepetition(?\DateTime $nextRepetition): void
    {
        $this->nextRepetition = $nextRepetition;
    }
}
