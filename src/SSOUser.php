<?php declare(strict_types=1);

namespace cyllenea\ssologin;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use Nette\Security\User;
use cyllenea\ssologin\Exception\GeneralException;

final class SSOUser extends User
{

    /** @var IUserStorage Session storage for current user */
    private $storage;

    /** @var IAuthenticator */
    private $authenticator;

    /** @var IAuthorizator */
    private $authorizator;

    public function __construct(IUserStorage $storage, Authenticator $authenticator = null, IAuthorizator $authorizator = null)
    {
        parent::__construct($storage, $authenticator, $authorizator);
        $this->storage = $storage;
        $this->authenticator = $authenticator;
        $this->authorizator = $authorizator;

        try {
			// Check if token is valid
            $this->authenticator->checkToken();
        } catch (GeneralException $e) {
            $this->logout(true);
        }
    }

	/**
	 * Logs out the user from the current session.
	 * @return void
	 * @param bool $clearIdentity
	 */
    public function logout($clearIdentity = false)
    {
        if ($this->isLoggedIn()) {
            $this->storage->setAuthenticated(false);
        }
        $this->storage->setIdentity(null);
        $this->authenticator->destroyAccessToken();
    }

	/**
	 * @param IIdentity|string|null $id
	 * @param string|null $password
	 * @return void
	 * @throws AuthenticationException if authentication was not successful
	 */
    public function login($id = null, $password = null)
    {
        $this->logout(true);
        if ($id instanceof IIdentity) {
            $this->storage->setIdentity($id);
            $this->storage->setAuthenticated(true);
        }
    }

}
