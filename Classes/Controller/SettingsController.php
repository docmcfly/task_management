<?php
namespace Cylancer\TaskManagement\Controller;

use Cylancer\TaskManagement\Domain\Model\Settings;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Cylancer\TaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\TaskManagement\Domain\Model\FrontendUser;
use Cylancer\TaskManagement\Service\FrontendUserService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 C.Gogolin <service@cylancer.net>
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
    public function __construct(
        FrontendUserService $frontendUserService,
        PersistenceManager $persistenceManager,
        FrontendUserRepository $frontendUserRepository
    ) {
        $this->frontendUserService = $frontendUserService;
        $this->persistenceManager = $persistenceManager;
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * @return ResponseInterface
     */
    public function showAction(): ResponseInterface
    {
        /** @var FrontendUser $frontendUser  */
        $frontendUser = $this->frontendUserService->getCurrentUser();
       // debug($frontendUser);
        if ($frontendUser != null) {
            $s = new Settings();
            $s->setInfoMailWhenRepeatedTaskAdded($frontendUser->getInfoMailWhenRepeatedTaskAdded());
            $this->view->assign('settings', $s);
        }
        return $this->htmlResponse();
    }

    /**
     *
     * @param Settings $settings
     * @return ResponseInterface
     */
    public function saveAction(Settings $settings): ResponseInterface
    {
        /** @var FrontendUser $frontendUser  */
        $frontendUser = $this->frontendUserService->getCurrentUser();
        if ($frontendUser != null) {
            $frontendUser->setInfoMailWhenRepeatedTaskAdded($settings->getInfoMailWhenRepeatedTaskAdded());
            $this->frontendUserRepository->update($frontendUser);
            $this->persistenceManager->persistAll();
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'show');
    }
}