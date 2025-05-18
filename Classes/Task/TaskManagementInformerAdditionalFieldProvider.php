<?php
namespace Cylancer\CyTaskManagement\Task;

use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

class TaskManagementInformerAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{

    private const TRANSLATION_PREFIX = 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.';

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initHintText(array &$additionalFields)
    {
        // Write the code for the field
        $fieldID = 'task_hint';
        $fieldCode = LocalizationUtility::translate('task.taskManagementInformer.hint.text', TaskManagementInformerTask::EXTENSION_NAME);
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'hint.title',
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    private function getDefault(string $key): string|int
    {
        switch ($key) {
            case TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID:
            case TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID:
            case TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS:
                return 0;
            case TaskManagementInformerTask::SUBJECT:
                return LocalizationUtility::translate(
                    TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'subject.default',
                    TaskManagementInformerTask::EXTENSION_NAME
                );
            case TaskManagementInformerTask::SENDER_NAME:
                return MailUtility::getSystemFromName();
            default:
                return '';
        }
    }

    private function setCurrentKey(array &$taskInfo, ?TaskManagementInformerTask $task, string $key): void
    {
        if (empty($taskInfo[$key])) {
            $taskInfo[$key] = $task != null ? $task->get($key) : $this->getDefault($key);
        }
    }


    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initIntegerAddtionalField(array &$taskInfo, $task,  string $key, array &$additionalFields)
    {
        $this->setCurrentKey($taskInfo, $task, $key);

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="number" min="0" max="99999" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initStringAddtionalField(array &$taskInfo, $task,  string $key, array &$additionalFields): void
    {
        $this->setCurrentKey($taskInfo, $task, $key);

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="text" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initUrlAddtionalField(array &$taskInfo, $task,  string $key, array &$additionalFields): void
    {
        $this->setCurrentKey($taskInfo, $task, $key);

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="url" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     * This method is used to define new fields for adding or editing a task
     * In this case, it adds a sleep time field
     *
     * @param array $taskInfo
     *            Reference to the array containing the info used in the add/edit form
     * @param TaskManagementInformerTask|null $task
     *            When editing, reference to the current task. NULL when adding.
     * @param SchedulerModuleController $schedulerModule
     *            Reference to the calling object (Scheduler's BE module)
     * @return array Array containing all the information pertaining to the additional fields
     */
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule): array
    {
        $additionalFields = [];
        $this->initHintText($additionalFields);
        $this->initIntegerAddtionalField($taskInfo, $task,  TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task,  TaskManagementInformerTask::SENDER_NAME, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task,  TaskManagementInformerTask::SUBJECT, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task,  TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS, $additionalFields);
        $this->initIntegerAddtionalField($taskInfo, $task, TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task,  TaskManagementInformerTask::SITE_IDENTIFIER, $additionalFields);

        return $additionalFields;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @return boolean
     */
    private function validatePageAdditionalField(array &$submittedData, string $key): bool
    {

        if (!$this->validatePage($submittedData[$key])) {
            $this->addMessage($this->getLanguageService()
                ->sL(TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'error.invalidPage.' . $key), ContextualFeedbackSeverity::ERROR);
            return false;
        }
        return true;
    }

    private function validatePage($pid): bool
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        return trim($pid) == strval(intval($pid)) && $pageRepository->getPage($pid, true) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @return boolean
     */
    private function validateFrontendUserGroupsAdditionalField(array &$submittedData, string $key): bool
    {
        $uids = GeneralUtility::intExplode(',', $submittedData[$key]);
        foreach ($uids as $uid) {
            if (!$this->validateFrontendGroup($uid)) {
                $this->addMessage(str_replace('%1', $uid, $this->getLanguageService()
                    ->sL(TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'error.invalidFrontendUserGroup.' . $key)), ContextualFeedbackSeverity::ERROR);
                return false;
            }
        }
        return true;
    }

    private function validateFrontendGroup($uid): bool
    {
        $frontendGroupsRepository = GeneralUtility::makeInstance(FrontendUserGroupRepository::class);
        return trim($uid) == strval(intval($uid)) && $frontendGroupsRepository->findByUid($uid) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @return boolean
     */
    private function validateUrlAdditionalField(array &$submittedData, string $key): bool
    {
        $url = $submittedData[$key];
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }


    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @return boolean
     */
    private function validateSitedField(array &$submittedData, string $key): bool
    {
        try {
            GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier($submittedData[$key]);
            return true;
        } catch (\Exception $e) {
            $this->addMessage($this->getLanguageService()
                ->sL(TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'error.siteNotFound.' . $key), ContextualFeedbackSeverity::ERROR);
            return false;
        }

    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @return boolean
     */
    private function validateRequiredField(array &$submittedData,  string $key): bool
    {
        if (empty($submittedData[$key])) {
            $this->addMessage($this->getLanguageService()
                ->sL(TaskManagementInformerAdditionalFieldProvider::TRANSLATION_PREFIX . 'error.required.' . $key), ContextualFeedbackSeverity::ERROR);
            return false;
        }
        return true;
    }


    /**
     * This method checks any additional data that is relevant to the specific task
     * If the task class is not relevant, the method is expected to return TRUE
     *
     * @param array $submittedData
     *            Reference to the array containing the data submitted by the user
     * @param SchedulerModuleController $schedulerModule
     *            Reference to the calling object (Scheduler's BE module)
     * @return bool TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData,SchedulerModuleController $schedulerModuleController): bool
    {
        $result = true;
        $result &= $this->validatePageAdditionalField($submittedData,  TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID);
        $result &= $this->validateFrontendUserGroupsAdditionalField($submittedData, TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS);
        $result &= $this->validatePageAdditionalField($submittedData,  TaskManagementInformerTask::INFO_MAIL_TARGET_PAGE_UID);
        $result &= $this->validateSitedField($submittedData,  TaskManagementInformerTask::SITE_IDENTIFIER);
        $result &= $this->validateRequiredField($submittedData,  TaskManagementInformerTask::SUBJECT);
        return $result;
    }

    /**
     * This method is used to save any additional input into the current task object
     * if the task class matches
     *
     * @param array $submittedData
     *            Array containing the data submitted by the user
     * @param TaskManagementInformerTask $task
     *            Reference to the current task object
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        $task->set($submittedData);
    }

    /**
     *
     * @return LanguageService|null
     */
    protected function getLanguageService(): ?LanguageService
    {
        return $GLOBALS['LANG'] ?? null;
    }

}
