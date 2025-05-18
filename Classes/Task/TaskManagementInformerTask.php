<?php
namespace Cylancer\CyTaskManagement\Task;

use Cylancer\CyTaskManagement\Domain\Repository\TaskRepository;
use Cylancer\CyTaskManagement\Domain\Model\Task;
use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserGroupRepository;
use Cylancer\CyTaskManagement\Domain\Model\FrontendUser;
use Cylancer\CyTaskManagement\Service\FrontendUserService;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;


class TaskManagementInformerTask extends AbstractTask
{

    // ------------------------------------------------------
    // input fields
    public const TASK_MANAGEMENT_STORAGE_UID = 'taskManagementStorageUid';

    public const INFORM_FE_USER_GROUP_UIDS = 'informFeUserGroupUids';

    public const INFO_MAIL_TARGET_PAGE_UID = 'infoMailTargetPageUid';

    public const SENDER_NAME = 'senderName';
    public const SUBJECT = 'subject';

    public const SITE_IDENTIFIER = 'siteIdentifier';
    public int|string $taskManagementStorageUid = 0;
    public array|string $informFeUserGroupUids = [];
    public int|string $infoMailTargetPageUid = 0;

    public string $senderName = '';
    public string $subject = '';

    public string $siteIdentifier = '';

    // ------------------------------------------------------

    public const EXTENSION_NAME = 'CyTaskManagement';

    // ------------------------------------------------------

    private ?FrontendUserService $frontendUserService = null;

    private ?PersistenceManager $persistenceManager;
    private ?FrontendUserRepository $frontendUserRepository = null;

    private ?TaskRepository $taskRepository = null;

    private ?PageRepository $pageRepository = null;

    private ?FrontendUserGroupRepository $frontendUserGroupRepository = null;

    private int $now = 0;

    private function initialize(): void
    {
        $this->now = time();

        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $feUserStorageUids = [];
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $qb */
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
        $this->frontendUserService = GeneralUtility::makeInstance(
            FrontendUserService::class,
            $this->frontendUserRepository,
            $this->frontendUserGroupRepository,
            GeneralUtility::makeInstance(Context::class),
            GeneralUtility::makeInstance(ConnectionPool::class),
        );

        if (empty($this->senderName)) {
            $this->senderName = LocalizationUtility::translate(
                'task.taskManagementInformer.informMail.senderName',
                TaskManagementInformerTask::EXTENSION_NAME
            );
        }
    }

    private function validate(): bool
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

    public function getAdditionalInformation()
    {
        return 'Tasks storage uid :' . $this->taskManagementStorageUid . //
            ' / sender name: "' . $this->senderName . '"' . //
            ' / subject: "' . $this->subject . '"' . //
            ' / frontend user groups: [ ' . implode(',', $this->informFeUserGroupUids) . ' ]' . //
            ' / site identifier: "' . $this->siteIdentifier . '"' . //
            ' / target page uid: ' . $this->infoMailTargetPageUid . //
            '';
    }

    private function areUserGroupsUidsValid(array $uids): bool
    {
        foreach ($uids as $uid) {
            if ($this->frontendUserGroupRepository->findByUid($uid) == null) {
                return false;
            }
        }
        return true;
    }

    private function isPageUidValid(int $id): bool
    {
        return $this->pageRepository->getPage($id, true) != null;
    }

    private function isUrlValid(string $url): bool
    {
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }


    private function isSiteIdentifierValid(string $siteIdentifier): bool
    {
        try {
            GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier($siteIdentifier);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function execute(): bool
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

    private function sendInfoMails(): void
    {
        foreach ($this->frontendUserService->getInformFrontendUser($this->informFeUserGroupUids) as $userUid) {
            $this->sendInfoMail($this->frontendUserRepository->findByUid($userUid));
        }
    }

    private function sendInfoMail(FrontendUser $frontendUser): void
    {
        if (filter_var($frontendUser->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $fluidEmail = GeneralUtility::makeInstance(FluidEmail::class);
            $fluidEmail
                ->setRequest($this->createRequest($this->siteIdentifier))
                ->to(new Address($frontendUser->getEmail(), $frontendUser->getFirstName() . ' ' . $frontendUser->getLastName()))
                ->from(new Address(MailUtility::getSystemFromAddress(), $this->senderName))
                ->subject($this->subject)
                ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
                ->setTemplate('TaskManagementInfoMail')
                ->assign('user', $frontendUser)
                ->assign('pageUid', $this->infoMailTargetPageUid);

            GeneralUtility::makeInstance(MailerInterface::class)->send($fluidEmail);
        }
    }



    private function createRequest(string $siteIdentifier): ServerRequest
    {
        $serverRequestFactory = GeneralUtility::makeInstance(ServerRequestFactoryInterface::class);
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier($siteIdentifier);
        $serverRequest = $serverRequestFactory->createServerRequest('GET', $site->getBase())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('site', $site)
            ->withAttribute('extbase', GeneralUtility::makeInstance(ExtbaseRequestParameters::class))
        ;
        return $serverRequest;
    }

    public function get(string $key): string|int
    {
        switch ($key) {
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                return intval($this->taskManagementStorageUid);
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                return implode(',', $this->informFeUserGroupUids);
            case TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID:
                return intval($this->infoMailTargetPageUid);
            case TaskManagementInformerTask::SENDER_NAME:
                return $this->senderName;
            case TaskManagementInformerTask::SUBJECT:
                return $this->subject;
            case TaskManagementInformerTask::SITE_IDENTIFIER:
                return $this->siteIdentifier;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }

    public function set(array $data): void
    {
        foreach ([// 
            TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID, //
            TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS, //
            TaskManagementInformerTask::SENDER_NAME,  //
            TaskManagementInformerTask::SUBJECT, //
            TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID, //
            TaskManagementInformerTask::SITE_IDENTIFIER//
        ] as $key) {
            $value = $data[$key];
            switch ($key) {
                case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
                    $this->taskManagementStorageUid = intval($value);
                    break;
                case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                    $this->informFeUserGroupUids = GeneralUtility::intExplode(',', $value);
                    break;
                case TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID:
                    $this->infoMailTargetPageUid = intval($value);
                    break;
                case TaskManagementInformerTask::SENDER_NAME:
                    $this->senderName = $value;
                    break;
                case TaskManagementInformerTask::SUBJECT:
                    $this->subject = $value;
                    break;
                case TaskManagementInformerTask::SITE_IDENTIFIER:
                    $this->siteIdentifier = $value;
                    break;
                default:
                    throw new \Exception("Unknown key: $key");
            }
        }
    }

    /**
     * 
     * @deprecated remove if all instances with the correct types are saved.
     * @return bool
     */
    public function save(): bool
    {
        $this->taskManagementStorageUid = intval($this->taskManagementStorageUid);
        $this->infoMailTargetPageUid = intval($this->infoMailTargetPageUid);
        if (is_string($this->informFeUserGroupUids)) {
            $this->informFeUserGroupUids = GeneralUtility::intExplode(',', $this->informFeUserGroupUids);
        }
        return parent::save();

    }


}