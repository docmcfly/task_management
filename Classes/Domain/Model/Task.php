<?php
declare(strict_types=1);
namespace Cylancer\CyTaskManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class Task extends AbstractEntity
{

    public const REPEAT_PERIOD_UNITS = [
        'days',
        'weeks',
        'months',
        'years'
    ];

    protected ?string $title = '';

    protected ?\DateTime $doneAt = null;

    protected ?FrontendUser $user = null;

    protected int $repeatPeriodCount = 1;

    protected ?string $repeatPeriodUnit = 'weeks';

    protected ?\DateTime $nextRepetition = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDoneAt(): ?\DateTime
    {
        return $this->doneAt;
    }

    public function setDoneAt(?\DateTime $doneAt): void
    {
        $this->doneAt = $doneAt;
    }

    public function setUser(?FrontendUser $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?FrontendUser
    {
        return $this->user;
    }

    public function getRepeatPeriodCount(): int
    {
        return $this->repeatPeriodCount;
    }

    public function setRepeatPeriodCount(int $repeatPeriodCount): void
    {
        $this->repeatPeriodCount = $repeatPeriodCount;
    }

    public function getRepeatPeriodUnit(): ?string
    {
        return $this->repeatPeriodUnit;
    }

    public function getTranslatedRepeatPeriodUnit(): ?string
    {
        return LocalizationUtility::translate('taskManagement.form.openTasks.repeatPeriodUnit.' . ($this->repeatPeriodCount == 1 ? 'singular.' : 'plural.') . $this->repeatPeriodUnit, 'cy_task_management');
    }

    public function setRepeatPeriodUnit(?string $repeatPeriodUnit): void
    {
        $this->repeatPeriodUnit = $repeatPeriodUnit;
    }

    public function getNextRepetition(): ?\DateTime
    {
        return $this->nextRepetition;
    }

    public function setNextRepetition(?\DateTime $nextRepetition): void
    {
        $this->nextRepetition = $nextRepetition;
    }
}
