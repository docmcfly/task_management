<?php
namespace Cylancer\CyTaskManagement\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Context\Context;
use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\CyTaskManagement\Domain\Model\FrontendUser;
use Cylancer\CyTaskManagement\Domain\Model\FrontendUserGroup;
use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserGroupRepository;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class FrontendUserService implements SingletonInterface
{

    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly FrontendUserGroupRepository $frontendUserGroupRepository,
        private readonly Context $context,
        private readonly ConnectionPool $connectionPool
    ) {
    }

    public static function getUid($object): int
    {
        return $object->getUid();
    }

    public function getCurrentUser(): ?FrontendUser
    {
        if (!$this->isLogged()) {
            return null;
        }
        return $this->frontendUserRepository->findByUid($this->getCurrentUserUid());
    }

    public function getCurrentUserUid(): int
    {
        if (!$this->isLogged()) {
            return false;
        }
        return $this->context->getPropertyFromAspect('frontend.user', 'id');
    }

    public function isLogged(): bool
    {
        return $this->context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    public function getTopGroups(FrontendUserGroup $userGroup): array
    {
        return $this->_getTopGroups($userGroup->getUid());
    }

    private function _getTopGroups(int $ug, array &$return = []): array
    {
        $return[] = $ug;
        $qb = $this->connectionPool->getQueryBuilderForTable('fe_groups');
        $s = $qb->select('fe_groups.uid')
            ->from('fe_groups')
            ->where($qb->expr()
                ->inSet('subgroup', $ug))
            ->executeQuery();
        while ($row = $s->fetchAssociative()) {
            $uid = intVal($row['uid']);
            if (!in_array($uid, $return)) {
                $return = array_unique(array_merge($return, $this->_getTopGroups($uid, $return)));
            }
        }
        return $return;
    }

    public function getInformFrontendUser(array $frontendUserGroupUids): array
    {

        $_frontendUserGroupUids = [];

        /**
         *
         * @var FrontendUserGroup $frontendUserGroup
         */
        foreach ($frontendUserGroupUids as $guid) {
            $_frontendUserGroupUids = array_merge($frontendUserGroupUids, $this->getTopGroups($this->frontendUserGroupRepository->findByUid($guid)));
        }
        $_frontendUserGroupUids = array_unique($_frontendUserGroupUids);
        $qb = $this->connectionPool->getQueryBuilderForTable('fe_user');
        $qb->select('uid')->from('fe_users');
        foreach ($_frontendUserGroupUids as $guid) {
            $qb->orWhere($qb->expr()
                ->inSet('usergroup', $guid));
        }
        $qb->andWhere($qb->expr()
            ->eq('info_mail_when_repeated_task_added', 1));
        // debug($qb->getSQL());
        $s = $qb->executeQuery();
        $return = [];
        while ($row = $s->fetchAssociative()) {
            $return[] = intVal($row['uid']);
        }
        return $return;
    }
}
