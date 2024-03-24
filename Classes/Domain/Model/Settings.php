<?php
namespace Cylancer\TaskManagement\Domain\Model;

/**
 * *
 *
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 C.Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Domain\Model
 */
class Settings
{

    /**
     *
     * @var boolean
     */
    protected $infoMailWhenRepeatedTaskAdded = true;

    /**
     *
     * @return boolean
     */
    public function getInfoMailWhenRepeatedTaskAdded(): bool
    {
        return $this->infoMailWhenRepeatedTaskAdded;
    }

    /**
     *
     * @param boolean $b
     * @return void
     */
    public function setInfoMailWhenRepeatedTaskAdded(bool $b): void
    {
        $this->infoMailWhenRepeatedTaskAdded = $b;
    }
}   