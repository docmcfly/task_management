<?php
namespace Cylancer\CyTaskManagement\Controller;

use Cylancer\CyTaskManagement\Domain\Model\Settings;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Cylancer\CyTaskManagement\Domain\Repository\FrontendUserRepository;
use Cylancer\CyTaskManagement\Domain\Model\FrontendUser;
use Cylancer\CyTaskManagement\Service\FrontendUserService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class SettingsController extends ActionController
{
    /**
     * 
     * @param FrontendUserService $frontendUserService
     * @param PersistenceManager $persistenceManager
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function __construct(
        private readonly FrontendUserService $frontendUserService,
        private readonly PersistenceManager $persistenceManager,
        private readonly FrontendUserRepository $frontendUserRepository
    ) {
    }

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