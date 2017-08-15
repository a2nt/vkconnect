<?php

/**
 * VK Connect Authenicator. Provides a tab in {@link Security::login()}.
 */
class VKAuthenticator extends Authenticator
{
    /**
     * Authentication is handled by VK rather than us this needs to
     * return the new member object which is created. Creation of the member
     * is handled by {@link VKConnect::onBeforeInt()}.
     *
     * @return false|Member
     */
    public static function authenticate($RAW_data, Form $form = null)
    {
        return ($member = Member::currentMember()) ? $member : false;
    }

    /**
     * Return the VK login form.
     *
     * @return Form
     */
    public static function get_login_form(Controller $controller)
    {
        return Object::create('VKLoginForm', $controller, 'VKLoginForm');
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
