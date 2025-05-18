<?php
namespace Cylancer\CyTaskManagement\Domain\Model;

/**
 * *
 *
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class Settings
{

    protected bool $infoMailWhenRepeatedTaskAdded = true;

    public function getInfoMailWhenRepeatedTaskAdded(): bool
    {
        return $this->infoMailWhenRepeatedTaskAdded;
    }

    public function setInfoMailWhenRepeatedTaskAdded(bool $b): void
    {
        $this->infoMailWhenRepeatedTaskAdded = $b;
    }
}