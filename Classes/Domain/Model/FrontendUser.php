<?php
namespace Cylancer\CyTaskManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 *
 * This file is part of the "Task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C.Gogolin <service@cylancer.net>
 *
 */
class FrontendUser extends AbstractEntity
{

    protected bool $currentlyOffDuty = false;

    /** @var ObjectStorage<FrontendUserGroup> */
    protected ObjectStorage $usergroup;

    protected ?string $username = '';

    protected ?string $name = '';

    protected ?string $firstName = '';

    protected ?string $lastName = '';

    protected ?string $email = '';

    protected bool $infoMailWhenRepeatedTaskAdded = true;

    public function __construct()
    {
        $this->usergroup = new ObjectStorage();
    }

    public function initializeObject()
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
    }


    public function setUsergroup(ObjectStorage $usergroup): void
    {
        $this->usergroup = $usergroup;
    }

    public function addUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->attach($usergroup);
    }

    public function removeUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->detach($usergroup);
    }

    public function getUsergroup(): ObjectStorage
    {
        return $this->usergroup;
    }


    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getInfoMailWhenRepeatedTaskAdded(): bool
    {
        return $this->infoMailWhenRepeatedTaskAdded;
    }

    public function setInfoMailWhenRepeatedTaskAdded(bool $b): void
    {
        $this->infoMailWhenRepeatedTaskAdded = $b;
    }


    private function getSubUserGroups(FrontendUserGroup $frontendUserGroup, array &$duplicateProtection): array
    {
        $return = [];
        /** @var FrontendUserGroup $sg */
        foreach ($frontendUserGroup->getSubgroup() as $sg) {
            if (!in_array($sg->getUid(), $duplicateProtection)) {
                $duplicateProtection[] = $sg->getUid();
                $return[$sg->getTitle()] = $sg;
                $return = array_merge($return, $this->getSubUserGroups($sg, $duplicateProtection));
            }
        }
        return $return;
    }
}
