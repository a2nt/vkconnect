<?php

/**
 * Main controller class to handle VK Connect implementations. Extends the
 * built in SilverStripe controller to add addition template functionality.
 */
class VKControllerExtension extends Extension
{
    /**
     * @config
     *
     * @var string
     */
    private static $app_id = '';

    /**
     * @config
     *
     * @var string
     */
    private static $api_secret = '';

    /**
     * @config
     *
     * @var bool
     */
    private static $create_member = true;

    /**
     * @config
     *
     * @var bool
     */
    private static $sync_member_details = true;

    /**
     * @var getjump\Vk\Core
     */
    private $helper;

    /**
     * @var getjump\Vk\Auth
     */
    private $session;

    /**
     * @var string
     */
    private $userID;

    public function init()
    {
        $this->getVKHelper();
    }

    public function getVKHelper()
    {
        if (!$this->helper) {
            $config = Config::inst();

            $appId = $config->get('VKControllerExtension', 'app_id');
            $secret = $config->get('VKControllerExtension', 'api_secret');

            if (!$appId || !$secret) {
                return false;
            }

            $this->helper = getjump\Vk\Core::getInstance()->apiVersion('5.5');

            $this->session = getjump\Vk\Auth::getInstance();
            $this->session->setAppId($appId)
                ->setScope('email')
                ->setSecret($secret)
                ->setRedirectUri($this->getCurrentPageURL());

            $accessToken = $this->session->startCallback();
            if ($accessToken) {
                $this->userID = $accessToken->userId;
                $this->helper->setToken($accessToken->token);

                // create, log in, sync
                $this->helper->request('users.get', [
                    'user_ids' => $this->getVKUserID(),
                    'fields' => $config->get('VKControllerExtension', 'fields')
                ])->each(function ($i, $user) use ($accessToken) {
                    $member = Member::get()->filter([
                        'VKUID' => $user->id
                    ])->first();

                    if (!$member) {
                        // see if we have a match based on email. From a
                        // security point of view, users have to confirm their
                        // email address in VK so doing a match up is fine
                        if (isset($user->email) && $user->email) {
                            $member = Member::get()->filter([
                                'Email' => $user->email,
                            ])->first();
                        }
                    }

                    if (!$member) {
                        $member = Injector::inst()->create('Member');
                    }

                    $user->VKAccessToken = $accessToken->token;
                    $member->syncVKDetails($user);
                    $member->logIn();
                });
            } else {
                $this->helper = false;
            }
        }

        return $this->helper;
    }

    public function getVKUserID()
    {
        return $this->userID;
    }

    /**
     * @return string
     */
    public function getVKLoginLink()
    {
        $this->getVKHelper();
        return $this->session->getUrl();
    }

    /**
     * @return string
     */
    public function getCurrentPageURL()
    {
        return Controller::join_links(Director::absoluteBaseURL(),$this->owner->Link());
    }
}
