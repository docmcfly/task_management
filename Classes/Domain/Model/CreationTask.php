<?php
declare(strict_types=1);
namespace Cylancer\CyTaskManagement\Domain\Model;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class CreationTask extends Task
{

    protected bool $useRepetition = false;

    public function getUseRepetition(): bool
    {
        return $this->useRepetition;
    }

    public function setUseRepetition($useRepetition): void
    {
        $this->useRepetition = $useRepetition;
    }
}
