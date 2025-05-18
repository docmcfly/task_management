<?php

declare(strict_types=1);

namespace Cylancer\CyTaskManagement\Upgrades;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('taskmanagement_taskmanagementUpgradeWizard')]
final class TaskManagementUpgradeWizard implements UpgradeWizardInterface
{

    private PersistenceManager $persistentManager;

    private ResourceFactory $resourceFactory;

    public function __construct()
    {
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }


    public function getTitle(): string
    {
        return 'Migration of the task management entries to the new database table';
    }

    public function getDescription(): string
    {
        return "Moves all old task management entries to the new database table ";
    }

    public function executeUpdate(): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connectionPool
            ->getConnectionForTable('tx_cytaskmanagement_domain_model_task')
            ->prepare('INSERT INTO `tx_cytaskmanagement_domain_model_task` '
                . '( `uid`, `pid`, `tstamp`, `crdate`, `deleted`, `hidden`, `starttime`, `endtime`, `sys_language_uid`, '
                 .'`l10n_parent`, `l10n_state`, `l10n_diffsource`, `t3ver_oid`, `t3ver_wsid`, `t3ver_state`, '
                .'`t3ver_stage`, `title`, `done_at`, `user`, `repeat_period_count`, `repeat_period_unit`, `next_repetition`)'
                . ' SELECT '
                . ' `uid`, `pid`, `tstamp`, `crdate`, `deleted`, `hidden`, `starttime`, `endtime`, `sys_language_uid`, '
                 .'`l10n_parent`, `l10n_state`, `l10n_diffsource`, `t3ver_oid`, `t3ver_wsid`, `t3ver_state`, '
                .'`t3ver_stage`, `title`, `done_at`, `user`, `repeat_period_count`, `repeat_period_unit`, `next_repetition`'
                . ' FROM `tx_taskmanagement_domain_model_task`')->executeStatement();

        return true;
    }

    /**
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool
            ->getConnectionForTable('tx_cytaskmanagement_domain_model_task')
            ->count(
                '*',
                'tx_cytaskmanagement_domain_model_task',
                [],
            ) == 0
            && $connectionPool
            ->getConnectionForTable('tx_taskmanagement_domain_model_task')
            ->count(
                '*',
                'tx_taskmanagement_domain_model_task',
                [],
            ) > 0
            ;
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}

