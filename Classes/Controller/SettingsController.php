<?php
namespace Cylancer\TaskManagement\Controller;

use Cylancer\TaskManagement\Domain\Model\Settings;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\TaskManagement\Domain\Model\FrontendUser;
use Cylancer\TaskManagement\Service\FrontendUserService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * This file is part of the "TaskManagement" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 C. Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Controller
 */
class SettingsController extends ActionController
{

    /** @var FrontendUserService */
    private $frontendUserService = null;

    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var FrontendUserRepository */
    private $frontendUserRepository = null;

    /**
     * 
     * @param FrontendUserService $frontendUserService
     * @param PersistenceManager $persistenceManager
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function __construct(FrontendUserService $frontendUserService, PersistenceManager $persistenceManager, //
    FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserService = $frontendUserService;
        $this->persistenceManager = $persistenceManager;
        $this->frontendUserRepository = $frontendUserRepository;
    }

    public function showAction(): void
    {
        /** @var FrontendUser $frontendUser  */
        $frontendUser = $this->frontendUserService->getCurrentUser();
        debug($frontendUser);
        if ($frontendUser != null) {
            $s = new Settings();
            $s->setInfoMailWhenRepeatedTaskAdded($frontendUser->getInfoMailWhenRepeatedTaskAdded());
            $this->view->assign('settings', $s);
        }
    }

    /**
     *
     * @param Settings $settings
     */
    public function saveAction(Settings $settings): void
    {
        /** @var FrontendUser $frontendUser  */
        $frontendUser = $this->frontendUserService->getCurrentUser();
        if ($frontendUser != null) {
            $frontendUser->setInfoMailWhenRepeatedTaskAdded($settings->getInfoMailWhenRepeatedTaskAdded());
            $this->frontendUserRepository->update($frontendUser);
            $this->persistenceManager->persistAll();
        }
        $this->forward('show');
    }
}