<?php

    namespace application\components;

    use \Yii;
    use \CException;
    use \CEvent as Event;
    use \application\models\db\User;

    /**
     * Web User
     *
     * @author      Zander Baldwin <mynameiszanders@gmail.com>
     * @license     MIT/X11 <http://j.mp/mit-license>
     * @copyright   Zander Baldwin <http://mynameis.zande.rs>
     */
    class WebUser extends \CWebUser
    {

        protected $user;


        /**
         * Constructor Method
         *
         * @access public
         * @return void
         */
        public function __construct()
        {
            \application\components\EventManager::attach($this);
        }


        /**
         * Initialisation Method
         *
         * @access public
         * @return void
         */
        public function init()
        {
            parent::init();
            // Raise an "onEndUser" event.
            $this->onStartUser(new Event($this));
            // Is the user logged in or not?
            if(!$this->getState('isGuest') || !$this->getIsGuest()) {
                // Load the database model for the currently logged in user so we can use their information throughout
                // the request.
                $this->user = User::model()->findByPk($this->getId());

                // Check a couple of things for security, like if the user is on the same IP address and browser that
                // they used to log in with. Also check that the user exists in the database, and has not somehow been
                // banned from the system.
                if(
                    $this->getState('userAgent') != $_SERVER['HTTP_USER_AGENT']
                 || $this->getState('loginIP') != $_SERVER['REMOTE_ADDR']
                 || !is_object($this->model())
                 || !$this->model()->active
                ) {
                    // If any of these simple checks fail, then log the user out immediately. Refer to the lengthy
                    // explaination in the Logout controller as to why we pass bool(false).
                    $this->logout(false);
                    // Set a flash message explaining that the user has been logged out (nothing worse than being kicked
                    // out without an explaination - people may complain about the system being faulty otherwise).
                    $this->setFlash(
                        'logout',
                        Yii::t(
                            'system60',
                            'You have been logged out because an attempted security breach has been detected. If this happens again please contact an administrator, as someone may be trying to access your account.'
                        )
                    );
                }

                // Raise an "onAuthenticated" event; specifying that the end-user is logged in.
                $this->onAuthenticated(new Event($this));
            }
            else {
                // Raise an "onGuest" event; specifying that the end-user is not logged in.
                $this->onGuest(new Event($this));
            }
        }


        /**
         * User Model
         *
         * @access public
         * @return User|null
         */
        public function model()
        {
            return is_object($this->user)
                ? $this->user
                : null;
        }


        /**
         * Get: Display Name
         *
         * @access public
         * @return string|null
         */
        public function getDisplayName()
        {
            return is_object($this->user)
                ? $this->user->displayName
                : null;
        }


        /**
         * Get: Full Name
         *
         * @access public
         * @return string|null
         */
        public function getFullName()
        {
            return is_object($this->user)
                ? $this->user->fullName
                : null;
        }


        /**
         * Get: Current Branch
         *
         * Returns the ID of the branch the currently logged-in user is viewing the site as. If a branch has not been
         * specified, then the ID of the branch the user actually belongs to is returned.
         *
         * @access public
         * @return integer
         */
        public function getBranch()
        {
            return is_object($this->user)
                ? (int) $this->getState('branch', $this->user->branch)
                : null;
        }


        /**
         * Switch Branch
         *
         * @access public
         * @param integer|Branch $branch
         * @return boolean
         */
        public function switchBranch($branch, $global = false)
        {
            if(!is_object($this->user)) {
                return false;
            }
            if(func_num_args() == 0) {
                $this->setState('branch', $this->user->branch);
                return true;
            }
            $sql = 'SELECT      `branch`.*
                    FROM        `{{branch}}`        AS `branch`
                    LEFT JOIN   `{{organisation}}`  AS `organisation`
                        ON      `organisation`.`id` =  `branch`.`organisation`
                    WHERE       `branch`.`id`       =  :branch';
            $params = array(
                ':branch'   => $branch instanceof \application\models\db\Branch
                    ? $branch->id
                    : $branch,
            );
            if(!$global) {
                $sql .= '   AND `organisation`.`id` =  (
                                    SELECT          `organisation`.`id`
                                    FROM            `{{organisation}}`      AS `organisation`
                                    LEFT JOIN       `{{branch}}`            AS `branch`
                                        ON          `branch`.`organisation` =  `organisation`.`id`
                                    LEFT JOIN       `{{user}}`              AS `user`
                                        ON          `user`.`branch`         =  `branch`.`id`
                                    WHERE           `user`.`id`             =  :user
                                    LIMIT           1
                                )';
                $params[':user'] = $this->id;
            }
            $branch = \application\models\db\Branch::model()->findBySql($sql, $params);
            if(!is_object($branch)) {
                return false;
            }
            $this->setState('branch', $branch->id);
            $this->setState('organisation', $branch->organisation);
            return true;
        }


        /**
         * Get: Current Organisation
         *
         * Returns the ID of the organisation the currently logged-in user is viewing the site as. If an organisation
         * has not been specified, then the ID of the organisation the user actually belongs to is returned.
         *
         * @access public
         * @return integer
         */
        public function getOrganisation()
        {
            return is_object($this->user)
                ? (int) $this->getState('organisation', $this->user->organisation)
                : null;
        }


        /**
         * Get: Priv
         *
         * @shortcut
         * @access public
         * @return integer
         */
        public function getPriv()
        {
            return $this->getPrivilege();
        }


        /**
         * Get: Privilege
         *
         * @access public
         * @return integer
         */
        public function getPrivilege()
        {
            return is_object($this->user) && isset($this->user->privilege)
                ? (int) $this->user->privilege
                : 1;
        }

        /* ================= *\
        |  EVENT DEFINITIONS  |
        \* ================= */

        /**
         * Event: End User
         *
         * @access public
         * @return void
         */
        public function onStartUser(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            //method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

        /**
         * Event: Start Authenticate Process
         *
         * @access public
         * @return void
         */
        public function onGuest(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            //method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

        /**
         * Event: Start Authenticate Process
         *
         * @access public
         * @return void
         */
        public function onAuthenticated(Event $event)
        {
            // Use __FUNCTION__ instead of __METHOD__, as the latter will also return the name of the class that the
            //method belongs to, which is not desired.
            if($this->hasEventHandler($name = __FUNCTION__)) {
                $this->raiseEvent($name, $event);
            }
        }

    }
