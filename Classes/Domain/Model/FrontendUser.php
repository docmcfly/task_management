<?php
namespace Cylancer\TaskManagement\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 *
 * This file is part of the "task management" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 *
 * @package Cylancer\TaskManagement\Domain\Model
 */
class FrontendUser extends AbstractEntity
{

    /**
     *
     * @var bool
     */
    protected $currentlyOffDuty = false;

    /**
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $usergroup;

    /**
     *
     * @var string
     */
    protected $telephone = '';

    /**
     *
     * @var string
     */
    protected $username = '';

    /**
     *
     * @var string
     */
    protected $name = '';

    /**
     *
     * @var string
     */
    protected $firstName = '';

    /**
     *
     * @var string
     */
    protected $lastName = '';

    /**
     *
     * @var string
     */
    protected $password = '';

    /**
     *
     * @var string
     */
    protected $email = '';

    /**
     * hidden personal duty roster groups
     *
     * @var ObjectStorage<FrontendUserGroup>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $hiddenPersonalDutyRosterGroups = null;

    /**
     *
     * @var boolean
     */
    protected $applyPlanningData = true;

    /**
     *
     * @var boolean
     */
    protected $infoMailWhenPersonalDutyRosterChanged = true;

    /**
     *
     * @var boolean
     */
    protected $infoMailWhenRepeatedTaskAdded = true;

    /**
     * Constructs a new Front-End User
     */
    public function __construct()
    {
        $this->usergroup = new ObjectStorage();
        $this->hiddenPersonalDutyRosterGroups = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
    }

    /**
     * Returns the allowDisplayImagePublic
     *
     * @return bool $allowDisplayImagePublic
     */
    public function getCurrentlyOffDuty(): bool
    {
        return $this->currentlyOffDuty;
    }

    /**
     * Sets the currentlyOffDuty
     *
     * @param bool $currentlyOffDuty
     * @return void
     */
    public function setCurrentlyOffDuty(bool $currentlyOffDuty): void
    {
        $this->currentlyOffDuty = $currentlyOffDuty;
    }

    /**
     * Sets the usergroups.
     * Keep in mind that the property is called "usergroup"
     * although it can hold several usergroups.
     *
     * @param ObjectStorage<FrontendUserGroup> $usergroup
     */
    public function setUsergroup(ObjectStorage $usergroup)
    {
        $this->usergroup = $usergroup;
    }

    /**
     * Adds a usergroup to the frontend user
     *
     * @param FrontendUserGroup $usergroup
     */
    public function addUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->attach($usergroup);
    }

    /**
     * Removes a usergroup from the frontend user
     *
     * @param FrontendUserGroup $usergroup
     */
    public function removeUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->detach($usergroup);
    }

    /**
     * Returns the usergroups.
     * Keep in mind that the property is called "usergroup"
     * although it can hold several usergroups.
     *
     * @return ObjectStorage<FrontendUserGroup> An object storage containing the usergroup
     */
    public function getUsergroup()
    {
        return $this->usergroup;
    }

    /**
     * Sets the telephone value
     *
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Returns the telephone value
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Sets the username value
     *
     * @param string $username
     */
    public function setUsername(String $username): void
    {
        $this->username = $username;
    }

    /**
     * Returns the username value
     *
     * @return string
     */
    public function getUsername(): String
    {
        return $this->username;
    }

    /**
     * Sets the name value
     *
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the name value
     *
     * @return string
     */
    public function getName(): String
    {
        return $this->name;
    }

    /**
     * Sets the firstName value
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the firstName value
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the lastName value
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Returns the lastName value
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the password value
     *
     * @param string $password
     */
    public function setPassword(String $password): String
    {
        $this->password = $password;
    }

    /**
     * Returns the password value
     *
     * @return string
     */
    public function getPassword(): String
    {
        return $this->password;
    }

    /**
     * Sets the email value
     *
     * @param string $email
     */
    public function setEmail(String $email): void
    {
        $this->email = $email;
    }

    /**
     * Returns the email value
     *
     * @return string
     */
    public function getEmail(): ?String
    {
        return $this->email;
    }

    /**
     *
     * @return boolean
     */
    public function getInfoMailWhenPersonalDutyRosterChanged(): bool
    {
        return $this->infoMailWhenPersonalDutyRosterChanged;
    }

    /**
     *
     * @param boolean $b
     */
    public function setInfoMailWhenPersonalDutyRosterChanged(bool $b): void
    {
        $this->infoMailWhenPersonalDutyRosterChanged = $b;
    }

    /**
     *
     * @param FrontendUserGroup $hiddenTargetGroup
     * @return void
     */
    public function addHiddenPersonalDutyRosterGroups(FrontendUserGroup $hiddenPersonalDutyRosterGroup): void
    {
        $this->hiddenPersonalDutyRosterGroups->attach($hiddenPersonalDutyRosterGroup);
    }

    /**
     *
     * @param FrontendUserGroup $hiddenTargetGroupToRemove
     * @return void
     */
    public function removeHiddenPersonalDutyRosterGroups(FrontendUserGroup $hiddenPersonalDutyRosterGroup): void
    {
        $this->hiddenPersonalDutyRosterGroups->detach($hiddenPersonalDutyRosterGroup);
    }

    /**
     * Returns the hiddenTargetGroups
     *
     * @return ObjectStorage<FrontendUserGroup> $hiddenTargetGroups
     */
    public function getHiddenPersonalDutyRosterGroups(): ObjectStorage
    {
        return $this->hiddenPersonalDutyRosterGroups;
    }

    /**
     * Sets the hiddenTargetGroups
     *
     * @param ObjectStorage<FrontendUserGroup> $hiddenTargetGroups
     * @return void
     */
    public function setHiddenPersonalDutyRosterGroups(ObjectStorage $hiddenPersonalDutyRosterGroups): void
    {
        $this->hiddenPersonalDutyRosterGroups = $hiddenPersonalDutyRosterGroups;
    }

    /**
     *
     * @return boolean
     */
    public function getApplyPlanningData(): bool
    {
        return $this->applyPlanningData;
    }

    /**
     *
     * @param boolean $b
     */
    public function setApplyPlanningData(bool $b): void
    {
        $this->applyPlanningData = $b;
    }

    /**
     *
     * @return boolean
     */
    public function getInfoMailWhenRepeatedTaskAdded(): bool
    {
        return $this->infoMailWhenRepeatedTaskAdded;
    }

    /**
     *
     * @param boolean $b
     * @return void
     */
    public function setInfoMailWhenRepeatedTaskAdded(bool $b): void
    {
        $this->infoMailWhenRepeatedTaskAdded = $b;
    }

    /**
     *
     * @return array
     */
    public function getAllSortedUserGroups(): array
    {
        $return = array();
        $duplicateProtection = array();

        /** @var FrontendUserGroup $frontendUserGroup **/
        foreach ($this->getUsergroup() as $frontendUserGroup) {
            $return[$frontendUserGroup->getTitle()] = $frontendUserGroup;
            $duplicateProtection[] = $frontendUserGroup->getUid();
            $return = array_merge($return, $this->getSubUserGroups($frontendUserGroup, $duplicateProtection));
        }
        ksort($return);
        return array_values($return);
    }

    /**
     *
     * @param FrontendUserGroup $userGroup
     * @param array $duplicateProtection
     * @return array
     */
    private function getSubUserGroups(FrontendUserGroup $frontendUserGroup, array &$duplicateProtection): array
    {
        $return = array();
        foreach ($frontendUserGroup->getSubgroup() as $sg) {
            if (! in_array($sg->getUid(), $duplicateProtection)) {
                $duplicateProtection[] = $sg->getUid();
                $return[$sg->getTitle()] = $sg;
                $return = array_merge($return, $this->getSubUserGroups($sg, $duplicateProtection));
            }
        }
        return $return;
    }
}
