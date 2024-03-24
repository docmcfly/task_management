<?php
declare(strict_types = 1);
namespace Cylancer\TaskManagement\Domain\Model;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 C.Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Domain\Model
 */
class CreationTask extends Task
{

    /**
     *
     * @var boolean
     */
    protected $useRepetition = false;

    /**
     *
     * @return boolean
     */
    public function getUseRepetition(): bool
    {
        return $this->useRepetition;
    }

    /**
     *
     * @param boolean $useRepetition
     */
    public function setUseRepetition($useRepetition): void
    {
        $this->useRepetition = $useRepetition;
    }
}
