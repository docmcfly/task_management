<?php
namespace Cylancer\TaskManagement\Task;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
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
use TYPO3\CMS\Core\Utility\MailUtility;

class TaskManagementInformerTask extends AbstractTask
{

    // ------------------------------------------------------
    // input fields
    const TASK_MANAGEMENT_STORAGE_UID = 'taskManagementStorageUid';

    const INFORM_FE_USER_GROUP_UIDS = 'informFeUserGroupUids';

    const INFO_MAIL_TARGET_PAGE_UID = 'infoMailTargetPageUid';

    const SENDER_NAME = 'senderName';

    const SITE_IDENTIFIER = 'siteIdentifier';

    /** @var int */
    public $taskManagementStorageUid = 0;

    /** @var string */
    public $informFeUserGroupUids = '';

    /** @var int */
    public $infoMailTargetPageUid = 0;

    /** @var string */
    public $senderName = '';

    /** @var string */
    public $siteIdentifier = '';

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
        $this->infoMailTargetPageUid = intval($this->infoMailTargetPageUid);
        $this->informFeUserGroupUids = GeneralUtility::intExplode(',', $this->informFeUserGroupUids);


        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $feUserStorageUids = [];
        /** @var QueryBuilder $qb */
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $s = $qb->select('uid')
            ->from('pages')
            ->where($qb->expr()
                ->eq('module', $qb->createNamedParameter('fe_users')))
            ->executeQuery();
        while ($row = $s->fetchAssociative()) {
            $feUserStorageUids[] = $row['uid'];
        }

        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        $this->frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class);
        $this->frontendUserRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = $this->frontendUserRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->frontendUserRepository->setDefaultQuerySettings($querySettings);

        $this->frontendUserGroupRepository = GeneralUtility::makeInstance(FrontendUserGroupRepository::class);
        $this->frontendUserGroupRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = $this->frontendUserGroupRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->frontendUserGroupRepository->setDefaultQuerySettings($querySettings);

        $this->taskRepository = GeneralUtility::makeInstance(TaskRepository::class);
        $this->taskRepository->injectPersistenceManager($this->persistenceManager);
        $querySettings = clone $this->taskRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds([
            $this->taskManagementStorageUid
        ]);
        $this->taskRepository->setDefaultQuerySettings($querySettings);
        $this->frontendUserService = GeneralUtility::makeInstance(FrontendUserService::class, $this->frontendUserRepository, $this->frontendUserGroupRepository);

        if (empty($this->senderName)) {
            $this->senderName = LocalizationUtility::translate(
                'task.taskManagementInformer.informMail.senderName',
                TaskManagementInformerTask::EXTENSION_NAME
            );
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
       
        $valid &= $this->isPageUidValid($this->taskManagementStorageUid);
        $valid &= $this->areUserGroupsUidsValid($this->informFeUserGroupUids);
        $valid &= $this->isPageUidValid($this->infoMailTargetPageUid);
        $valid &= $this->isSiteIdentifierValid($this->siteIdentifier);

        return $valid;
    }


    /**
     * This method returns the sleep duration as additional information
     *
     * @return string Information to display
     */
    public function getAdditionalInformation()
    {
        return 'Tasks storage uid :' . $this->taskManagementStorageUid . //
            ' / rontend user groups: ' . $this->informFeUserGroupUids . //
            ' / site identifier: ' . $this->siteIdentifier . //
            ' / target page uid: ' . $this->infoMailTargetPageUid . //
            '';
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
        return $this->pageRepository->getPage($id, true) != null;
    }

    /**
     *
     * @param string $url
     * @return bool
     */
    private function isUrlValid(string $url): bool
    {
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }


    /**
     *
     * @return boolean
     */
    private function isSiteIdentifierValid(string $siteIdentifier): bool
    {
        try {
            GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier($siteIdentifier);
        } catch (\Exception $e) {
            return false;
        }
        return true;
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
            //            $this->persistenceManager->persistAll();
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
            $this->sendInfoMail($this->frontendUserRepository->findByUid($userUid));
        }
    }

    private function sendInfoMail(FrontendUser $frontendUser)
    {
        if (filter_var($frontendUser->getEmail(), FILTER_VALIDATE_EMAIL)) {

            $fluidEmail = GeneralUtility::makeInstance(FluidEmail::class);
            $fluidEmail
                ->setRequest($this->createRequest($this->siteIdentifier))
                ->to(new Address($frontendUser->getEmail(), $frontendUser->getFirstName() . ' ' . $frontendUser->getLastName()))
                ->from(new Address(MailUtility::getSystemFromAddress(), $this->senderName))
                ->subject(LocalizationUtility::translate('task.taskManagementInformer.informMail.senderName', TaskManagementInformerTask::EXTENSION_NAME))
                ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
                ->setTemplate('TaskManagementInfoMail')
                ->assign('user', $frontendUser)
                ->assign('pageUid', $this->infoMailTargetPageUid)
            ;
        //    GeneralUtility::makeInstance(MailerInterface::class)->send($fluidEmail);
        }
    }


    private function createRequest(string $siteIdentifier): RequestInterface
    {
        $serverRequestFactory = GeneralUtility::makeInstance(ServerRequestFactoryInterface::class);
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier($siteIdentifier);
        $serverRequest = $serverRequestFactory->createServerRequest('GET', $site->getBase())
            ->withAttribute('applicationType', \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('site', $site)
            ->withAttribute('extbase', new \TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters());
        $request = GeneralUtility::makeInstance(Request::class, $serverRequest);
        //$GLOBALS['TYPO3_REQUEST'] = $request;
        if (!isset($GLOBALS['TYPO3_REQUEST'])) {
            $GLOBALS['TYPO3_REQUEST'] = $request;
        }
        return $request;
    }



    /**
     *
     * @param string $key
     * @throws \Exception
     * @return number|string
     */
    public function get(string $key)
    {
        switch ($key) {
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                return $this->taskManagementStorageUid;
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                return $this->informFeUserGroupUids;
            case TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID:
                return $this->infoMailTargetPageUid;
            case TaskManagementInformerTask::SENDER_NAME:
                return $this->senderName;
            case TaskManagementInformerTask::SITE_IDENTIFIER:
                return $this->siteIdentifier;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }

    /**
     *
     * @param string $key
     * @param string|number $value
     * @throws \Exception
     */
    public function set(string $key, $value)
    {
        switch ($key) {
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                $this->taskManagementStorageUid = $value;
                break;
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                $this->informFeUserGroupUids = $value;
                break;
            case TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID:
                $this->infoMailTargetPageUid = $value;
                break;
            case TaskManagementInformerTask::SENDER_NAME:
                $this->senderName = $value;
                break;
            case TaskManagementInformerTask::SITE_IDENTIFIER:
                $this->siteIdentifier = $value;
                break;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }
}