<?php
namespace Cylancer\TaskManagement\Service;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Context\Context;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\TaskManagement\Domain\Model\FrontendUser;
use Cylancer\TaskManagement\Domain\Model\FrontendUserGroup;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserGroupRepository;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 C. Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Service
 */
class FrontendUserService implements SingletonInterface
{

    /** @var FrontendUserRepository   */
    private $frontendUserRepository = null;
    
    /** @var FrontendUserGroupRepository */
    private $frontendUserGroupRepository = null;
    
   /**
     * 
     * @param FrontendUserRepository $frontendUserRepository
     * @param FrontendUserGroupRepository $frontendUserGroupRepository
     */
    public function __construct(FrontendUserRepository $frontendUserRepository, FrontendUserGroupRepository $frontendUserGroupRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    /**
     * @return int
     */
    public static function getUid($object): int
    {
        return $object->getUid();
    }

    /**
     *
     * @return FrontendUser Returns the current frontend user
     */
    public function getCurrentUser():? FrontendUser
    {
        debug($this->getCurrentUserUid());
        if (! $this->isLogged()) {
            return null;
        }
        return $this->frontendUserRepository->findByUid($this->getCurrentUserUid());
    }

    /**
     *
     * @return int
     */
    public function getCurrentUserUid(): int
    {
        if (! $this->isLogged()) {
            return false;
        }
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'id');
    }

    /**
     * Check if the user is logged
     *
     * @return bool
     */
    public function isLogged(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

   

    /**
     *
     * @param string $table
     * @return QueryBuilder
     */
    protected function getQueryBuilder(String $table): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

  

    /**
     * Returns all groups from the frontend user group to all his leafs in the hierachy tree...
     *
     * @param FrontendUserGroup $userGroup
     * @return array
     */
    public function getTopGroups(FrontendUserGroup $userGroup): array
    {
        return $this->_getTopGroups($userGroup->getUid());
    }

    private function _getTopGroups(int $ug, array &$return = []): array
    {
        $return[] = $ug;
        $qb = $this->getQueryBuilder('fe_groups');
        $s = $qb->select('fe_groups.uid')
            ->from('fe_groups')
            ->where($qb->expr()
            ->inSet('subgroup', $ug))
            ->execute();
        while ($row = $s->fetch()) {
            $uid = intVal($row['uid']);
            if (! in_array($uid, $return)) {
                $return = array_unique(array_merge($return, $this->_getTopGroups($uid, $return)));
            }
        }
        return $return;
    }

    public function getInformFrontendUser(array $frontendUserGroupUids)
    {

        // debug($frontendUserGroupUids);
        $_frontendUserGroupUids = array();

        /**
         *
         * @var FrontendUserGroup $frontendUserGroup
         */
        foreach ($frontendUserGroupUids as $guid) {
            // debug($guid);
            $_frontendUserGroupUids = array_merge($frontendUserGroupUids, $this->getTopGroups($this->frontendUserGroupRepository->findByUid($guid)));
        }
        $_frontendUserGroupUids = array_unique($_frontendUserGroupUids);
        $qb = $this->getQueryBuilder('fe_user');
        $qb->select('uid')->from('fe_users');
        foreach ($_frontendUserGroupUids as $guid) {
            $qb->orWhere($qb->expr()
                ->inSet('usergroup', $guid));
        }
        $qb->andWhere($qb->expr()
            ->eq('info_mail_when_repeated_task_added', 1));
        // debug($qb->getSQL());
        $s = $qb->execute();
        $return = array();
        while ($row = $s->fetch()) {
            $return[] = intVal($row['uid']);
        }
        return $return;
    }
}
