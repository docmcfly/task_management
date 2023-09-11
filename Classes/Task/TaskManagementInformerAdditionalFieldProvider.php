<?php
namespace Cylancer\TaskManagement\Task;

use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\Enumeration\Action;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;

class TaskManagementInformerAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{

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
        $fieldCode = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('task.taskManagementInformer.hint.text', TaskManagementInformerTask::EXTENSION_NAME);
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.hint.title',
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initIntegerAddtionalField(array &$taskInfo, $task, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($taskInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new task and if field is empty, set default sleep time
                $taskInfo[$key] = 0;
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $taskInfo[$key] = $task->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $taskInfo[$key] = 0;
            }
        }

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="number" min="0" max="99999" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.' . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initStringAddtionalField(array &$taskInfo, $task, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($taskInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new task and if field is empty, set default sleep time
                $taskInfo[$key] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $taskInfo[$key] = $task->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $taskInfo[$key] = '';
            }
        }

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="text" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.' . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $taskInfo
     * @param TaskManagementInformerTask|null $task
     * @param SchedulerModuleController $schedulerModule
     * @param string $key
     * @param array $additionalFields
     * @return void
     */
    private function initUrlAddtionalField(array &$taskInfo, $task, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($taskInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new task and if field is empty, set default sleep time
                $taskInfo[$key] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $taskInfo[$key] = $task->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $taskInfo[$key] = '';
            }
        }

        // Write the code for the field
        $fieldID = 'task_' . $key;
        $fieldCode = '<input type="url" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $taskInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.' . $key,
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
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule)
    {
        $additionalFields = [];
        $this->initHintText($additionalFields);
        $this->initIntegerAddtionalField($taskInfo, $task, $schedulerModule, TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task, $schedulerModule, TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS, $additionalFields);
        $this->initStringAddtionalField($taskInfo, $task, $schedulerModule, TaskManagementInformerTask::SENDER_NAME, $additionalFields);
        $this->initUrlAddtionalField($taskInfo, $task, $schedulerModule, TaskManagementInformerTask::INFO_MAIL_TARGET_URL, $additionalFields);

        // debug($additionalFields);
        return $additionalFields;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validatePageAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $result = true;
        if (! $this->validatePage($submittedData[$key])) {
            $this->addMessage($this->getLanguageService()
                ->sL('LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.error.invalidPage.' . $key), FlashMessage::ERROR);
            $result = false;
        }

        return $result;
    }

    private function validatePage($pid)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $pageRepository = $objectManager->get(PageRepository::class);
        return trim($pid) == strval(intval($pid)) && $pageRepository->getPage($pid) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validateFrontendUserGroupsAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $result = true;
        $uids = GeneralUtility::intExplode(',', $submittedData[$key]);
        foreach ($uids as $uid) {
            if (! $this->validateFrontendGroup($uid)) {
                $this->addMessage(str_replace('%1', $uid, $this->getLanguageService()
                    ->sL('LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.error.invalidFrontendUserGroup.' . $key)), FlashMessage::ERROR);
                $result = false;
            }
        }
        return $result;
    }

    private function validateFrontendGroup($uid)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /**
         *
         * @var FrontendUserGroupRepository $frontendGroupsRepository
         */
        $frontendGroupsRepository = $objectManager->get(FrontendUserGroupRepository::class);
        return trim($uid) == strval(intval($uid)) && $frontendGroupsRepository->findByUid($uid) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validateUrlAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $url = $submittedData[$key];
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
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
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule)
    {
        $result = true;
        $result &= $this->validatePageAdditionalField($submittedData, $schedulerModule, TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID);
        $result &= $this->validateFrontendUserGroupsAdditionalField($submittedData, $schedulerModule, TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS);
        $result &= $this->validateUrlAdditionalField($submittedData, $schedulerModule, TaskManagementInformerTask::INFO_MAIL_TARGET_URL);
        return $result;
    }

    /**
     *
     * @param array $submittedData
     * @param AbstractTask $task
     * @param String $key
     * @return void
     */
    public function saveAdditionalField(array $submittedData, AbstractTask $task, String $key)
    {
        /**
         *
         * @var TaskManagementInformerTask $task
         */
        $task->set($key, $submittedData[$key]);
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
        $this->saveAdditionalField($submittedData, $task, TaskManagementInformerTask::TASK_MANAGEMENT_STORAGE_UID);
        $this->saveAdditionalField($submittedData, $task, TaskManagementInformerTask::INFORM_FE_USER_GROUP_UIDS);
        $this->saveAdditionalField($submittedData, $task, TaskManagementInformerTask::SENDER_NAME);
        $this->saveAdditionalField($submittedData, $task, TaskManagementInformerTask::INFO_MAIL_TARGET_URL);
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
