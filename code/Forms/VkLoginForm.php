<?php

use SilverStripe\Security\Member;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Control\Session;
use SilverStripe\Security\MemberAuthenticator\MemberLoginForm;

/**
 * Return a Vk Login Form for the website.
 */
class VkLoginForm extends MemberLoginForm
{
    protected $authenticator_class = 'VkAuthenticator';

    public function __construct($controller, $name, $fields = null, $actions = null, $checkCurrentUser = true)
    {
        if ($checkCurrentUser && Member::currentUser() && Member::logged_in_session_exists()) {
            $fields = FieldList::create(
                HiddenField::create('AuthenticationMethod', null, $this->authenticator_class, $this)
            );

            $actions = FieldList::create(
                FormAction::create('logout', _t('Member.BUTTONLOGINOTHER', 'Log in as someone else'))
            );
        } else {
            $fields = FieldList::create(
                LiteralField::create('VkLoginIn', "<fb:login-button scope='".$controller->getVkPermissions()."'></fb:login-button>")
            );

            $actions = FieldList::create(
                LiteralField::create('VkLoginLink', "<!-- <a href='".$controller->getVkLoginLink()."'>"._t('VkLoginForm.LOGIN', 'Login').'</a> -->')
            );
        }

        $backURL = (isset($_REQUEST['BackURL'])) ? $_REQUEST['BackURL'] : Session::get('BackURL');

        if (isset($backURL)) {
            $fields->push(HiddenField::create('BackURL', 'BackURL', $backURL));
        }

        return parent::__construct($controller, $name, $fields, $actions);
    }
}
