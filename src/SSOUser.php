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

        $this->onLoggedOut[] = function() {
            $this->authenticator->destroyAccessToken();
        };

    }

	/**
	 * @param IIdentity|string|null $id
	 * @param string|null $password
	 * @return void
	 * @throws AuthenticationException if authentication was not successful
	 */
    public function login($user, string $password = null): void
    {
        $this->logout(true);
        if ($id instanceof IIdentity) {
            $this->storage->setIdentity($user);
            $this->storage->setAuthenticated(true);
        }
        $this->onLoggedIn($this);
    }

}
