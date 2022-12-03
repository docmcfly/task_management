<?php
namespace Cylancer\TaskManagement\Task;

use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use Cylancer\TaskManagement\Domain\Repository\TaskRepository;
use Cylancer\TaskManagement\Domain\Model\Task;
use Cylancer\TaskManagement\Service\EmailSendService;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserGroupRepository;
use Cylancer\TaskManagement\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Cylancer\TaskManagement\Service\FrontendUserService;

class TaskManagementInformerTask extends AbstractTask
{

    // ------------------------------------------------------
    // input fields
    const TASK_MANAGEMENT_STORAGE_UID = 'taskManagementStorageUid';

    const INFORM_FE_USER_GROUP_UIDS = 'informFeUserGroupUids';

    const INFO_MAIL_TARGET_URL = 'infoMailTargetUrl';

    const SENDER_NAME = 'senderName';

    /** @var int */
    public $taskManagementStorageUid = 0;

    /** @var String */
    public $informFeUserGroupUids = '';

    /** @var String */
    public $infoMailTargetUrl = 'https://cylancer.net';

    /** @var String */
    public $senderName = '';

    // ------------------------------------------------------
    // debug switch
    const DISABLE_PERSISTENCE_MANAGER = false;

    const EXTENSION_NAME = 'TaskManagement';

    // ------------------------------------------------------

    /** @var FrontendUserService */
    private $frontendUserService = null;

    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var FrontendUserRepository */
    private $frontendUserRepository = null;

    /** @var TaskRepository */
    private $taskRepository = null;

    /** @var PageRepository */
    private $pageRepository = null;

    /** @var FrontendUserGroupRepository */
    private $frontendUserGroupRepository = null;

    /**  @var EmailSendService */
    public $emailSendService = null;

    /** @var array */
    private $targetGroups = null;

    /** @var int */
    private $now;

    private function initialize()
    {
        $this->now = time();

        $this->taskManagementStorageUid = intval($this->taskManagementStorageUid);
        $this->informFeUserGroupUids = GeneralUtility::intExplode(',', $this->informFeUserGroupUids);

        /**
         *
         * @var ObjectManager $objectManager
         * @deprecated $objectManager
         */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $feUserStorageUids = [];
        /** @var QueryBuilder $qb */
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $s = $qb->select('uid')
            ->from('pages')
            ->where($qb->expr()
            ->eq('module', $qb->createNamedParameter('fe_users')))
            ->execute();
        while ($row = $s->fetch()) {
            $feUserStorageUids[] = $row['uid'];
        }

        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $this->frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class, $objectManager);
        $this->frontendUserRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = $this->frontendUserRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->frontendUserRepository->setDefaultQuerySettings($querySettings);

        $this->frontendUserGroupRepository = GeneralUtility::makeInstance(FrontendUserGroupRepository::class, $objectManager);
        $this->frontendUserGroupRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = $this->frontendUserGroupRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->frontendUserGroupRepository->setDefaultQuerySettings($querySettings);

        $this->taskRepository = GeneralUtility::makeInstance(TaskRepository::class, $objectManager);
        $this->taskRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = $this->taskRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds([
            $this->taskManagementStorageUid
        ]);
        $this->taskRepository->setDefaultQuerySettings($querySettings);

        $this->frontendUserService = GeneralUtility::makeInstance(FrontendUserService::class, $this->frontendUserRepository, $this->frontendUserGroupRepository);

        $this->emailSendService = GeneralUtility::makeInstance(EmailSendService::class);

        if (empty($this->senderName)) {
            $this->senderName = LocalizationUtility::translate( //
            'task.taskManagementInformer.informMail.senderName', //
            TaskManagementInformerTask::EXTENSION_NAME);
        }
    }

    private function validate()
    {
        $valid = true;

        $valid &= $this->pageRepository != null;
        $valid &= $this->taskRepository != null;
        $valid &= $this->frontendUserRepository != null;
        $valid &= $this->frontendUserGroupRepository != null;
        $valid &= $this->frontendUserService != null;
        $valid &= $this->emailSendService != null;

        $valid &= $this->isPageUidValid($this->taskManagementStorageUid);
        $valid &= $this->areUserGroupsUidsValid($this->informFeUserGroupUids);
        $valid &= $this->isUrlValid($this->infoMailTargetUrl);
        return $valid;
    }

    /**
     *
     * @param array $uids
     * @return bool
     */
    private function areUserGroupsUidsValid(array $uids): bool
    {
        foreach ($uids as $uid) {
            if ($this->frontendUserGroupRepository->findByUid($uid) == null) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param int $id
     * @return bool
     */
    private function isPageUidValid(int $id): bool
    {
        return $this->pageRepository->getPage($id) != null;
    }

    /**
     *
     * @param String $url
     * @return bool
     */
    private function isUrlValid(String $url): bool
    {
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     *
     * @return \DateTime
     */
    private function createNow(): \DateTime
    {
        $return = new \DateTime();
        $return->setTimestamp($this->now);
        return $return;
    }

    public function execute()
    {
        $this->initialize();

        /** @var Task $task */
        /** @var Task $replica */
        if ($this->validate()) {
            $sendInfoMail = false;
            foreach ($this->taskRepository->findRepeatTasks() as $task) {
                $replica = new Task();
                $replica->setTitle($task->getTitle());
                $replica->setUser($task->getUser());
                $replica->setRepeatPeriodCount($task->getRepeatPeriodCount());
                $replica->setRepeatPeriodUnit($task->getRepeatPeriodUnit());
                $replica->setPid($this->taskManagementStorageUid);
                $this->taskRepository->add($replica);

                $task->setNextRepetition(null);
                $this->taskRepository->update($task);
                $sendInfoMail = true;
            }
            $this->persistenceManager->persistAll();
            if ($sendInfoMail) {
                $this->sendInfoMails();
            }

            return true;
        } else {
            return false;
        }
    }

    private function sendInfoMails()
    {
        foreach ($this->frontendUserService->getInformFrontendUser($this->informFeUserGroupUids) as $userUid) {
            debug($userUid);
            $this->sendInfoMail($this->frontendUserRepository->findByUid($userUid));
        }
    }

    private function sendInfoMail(FrontendUser $frontendUser)
    {
        if (filter_var($frontendUser->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $recipient = [
                $frontendUser->getEmail() => $frontendUser->getFirstName() . ' ' . $frontendUser->getLastName()
            ];
            $sender = [
                \TYPO3\CMS\Core\Utility\MailUtility::getSystemFromAddress() => $this->senderName
            ];
            $subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('task.taskManagementInformer.informMail.senderName', TaskManagementInformerTask::EXTENSION_NAME);

            $data = [
                'user' => $frontendUser,
                'url' => $this->infoMailTargetUrl
            ];

            $this->emailSendService->sendTemplateEmail($recipient, $sender, [], $subject, 'TaskManagementInfoMail', TaskManagementInformerTask::EXTENSION_NAME, $data);
        }
    }

    /**
     * This method returns the sleep duration as additional information
     *
     * @return String Information to display
     */
    public function getAdditionalInformation(): String
    {
        return 'Tasks storage uid: ' . $this->taskManagementStorageUid . ' / Frontend user group: ' . $this->informFeUserGroupUids;
    }

    /**
     *
     * @param String $key
     * @throws \Exception
     * @return number|String
     */
    public function get(String $key)
    {
        switch ($key) {
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                return $this->taskManagementStorageUid;
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                return $this->informFeUserGroupUids;
            case TaskManagementInformerTask::INFO_MAIL_TARGET_URL:
                return $this->infoMailTargetUrl;
            case TaskManagementInformerTask::SENDER_NAME:
                return $this->senderName;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }

    /**
     *
     * @param String $key
     * @param String|number $value
     * @throws \Exception
     */
    public function set(String $key, $value)
    {
        switch ($key) {
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                $this->taskManagementStorageUid = $value;
                break;
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                $this->informFeUserGroupUids = $value;
                break;
            case TaskManagementInformerTask::INFO_MAIL_TARGET_URL:
                $this->infoMailTargetUrl = $value;
                break;
            case TaskManagementInformerTask::SENDER_NAME:
                $this->senderName = $value;
                break;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }
}


