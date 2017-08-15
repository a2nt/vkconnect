<?php

namespace A2nt\VkConnect;

use SilverStripe\Forms\Form;
use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;

/**
 * VK Connect Authenicator. Provides a tab in {@link Security::login()}.
 */
class VKAuthenticator extends MemberAuthenticator
{
    /**
     * Authentication is handled by VK rather than us this needs to
     * return the new member object which is created. Creation of the member
     * is handled by {@link VKConnect::onBeforeInt()}.
     *
     * @return false|Member
     */
    /*public function authenticate(array $data, HTTPRequest $request, ValidationResult &$result = null)
    {
        return ($member = Member::currentMember()) ? $member : false;
    }*/

    /**
     * Return the VK login form.
     *
     * @return Form
     */
    public static function get_login_form(Controller $controller)
    {
        return VKLoginForm::create($controller, 'VKLoginForm');
    }

    /**
     * Return the name for the VK tab.
     *
     * @return string
     */
    public static function get_name()
    {
        return _t('VKAuthenicator.TITLE', 'VK Connect');
    }
}
