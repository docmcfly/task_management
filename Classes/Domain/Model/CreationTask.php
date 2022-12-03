<?php
declare(strict_types = 1);
namespace Cylancer\TaskManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

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
