<?php

/**
 * VK member class to wrap the member functionality of the VK
 * members into the member object.
 *
 * An extension to the built in {@link Member} class this adds the fields which
 * may be required as part of the member
 */
class VKMemberExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'VKUID' => 'Varchar(200)',
        'VKLink' => 'Varchar(200)',
        'VKTimezone' => 'Varchar(200)',
        'VKAccessToken' => 'Varchar',
    ];

    public function getCMSFields(FieldList $fields)
    {
        if ($fields->getField('VKUID')) {
            $fields->makeFieldReadonly('VKUID');
            $fields->makeFieldReadonly('VKLink');
            $fields->makeFieldReadonly('VKTimezone');
        }
    }

    /**
     * Sync the new data from a users VK profile to the member database.
     *
     * @param stdClass $user
     * @param bool     $override Flag to whether we override fields like first name
     */
    public function updateVKFields($user, $override = true)
    {
        $this->owner->VKLink = 'https://vk.com/'.$user->domain;
        $this->owner->VKUID = $user->id;
        $this->owner->VKTimezone = $user->timezone;

        if ($override) {
            if (
                isset($user->email) && $user->email
                && (!$this->owner->Email || !Email::is_valid_address($this->owner->Email))
            ) {
                $this->owner->Email = $user->email;
            }

            $this->owner->FirstName = $user->first_name;
            $this->owner->Surname = $user->last_name;
        }
        $this->owner->VKAccessToken = $user->VKAccessToken;

        $this->owner->extend('onUpdateVKFields', $user);
    }

    /**
     * @param stdClass $user
     *
     * @return Member
     */
    public function syncVKDetails($user)
    {
        $override = Config::inst()->get('VKControllerExtension', 'sync_member_details');
        $create = Config::inst()->get('VKControllerExtension', 'create_member');

        $this->owner->updateVKFields($user, $override);

        // sync details	to the database
        if (($this->owner->ID && $override) || $create) {
            if ($this->owner->isChanged()) {
                $this->owner->write();
            }
        }

        // ensure members are in the correct groups
        $groups = Config::inst()->get('VKControllerExtension', 'member_groups');
        if ($groups) {
            foreach ($groups as $group) {
                $this->owner->addToGroupByCode($group);
            }
        }

        return $this->owner;
    }
}
