<?php

namespace Mealz\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Mealz\UserBundle\Entity\Login;
use Mealz\UserBundle\Entity\Profile;
use Mealz\UserBundle\User\LdapUser;
use Mealz\UserBundle\User\UserInterface as MealzUserInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 * actions that might be taken after login
 */
class PostLogin {

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var Logger
	 */
	protected $logger;

	public function __construct(EntityManager $em, Logger $logger) {
		$this->entityManager = $em;
		$this->logger = $logger;
	}

	/**
	 * check if a profile for that user exists, and if not create one
	 *
	 * @param $user
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function assureProfileExists($user) {
		if(!$user instanceof SymfonyUserInterface) {
			throw new \InvalidArgumentException(sprintf(
				'The given user of class %s does not implement the Symfony\\Component\\Security\\Core\\User\\UserInterface',
				get_class($user)
			));
		}
		if(!$user instanceof MealzUserInterface) {
			throw new \InvalidArgumentException(sprintf(
				'The given user of class %s does not implement the Mealz\\UserBundle\\User\\UserInterface',
				get_class($user)
			));
		}

		if($user->getProfile()) {
			if(!$user->getProfile() instanceof Profile) {
				throw new \RuntimeException(sprintf(
					'Profile of user %s is not of class %s but %s. I\'m confused.',
					$user,
					get_class(new Profile),
					get_class($user->getProfile())
				));
			}
			return;
		} else {
			$username = $user->getUsername();

			$profile = $this->entityManager->find('MealzUserBundle:Profile', $username);
			if(!$profile) {
				$profile = $this->createProfile($user);
			}

			$user->setProfile($profile);
			if($user instanceof Login) {
				// persist relation to profile
				$this->entityManager->persist($user);
				$this->entityManager->flush();
			}
		}

	}

	/**
	 * @param \Symfony\Component\Security\Core\User\UserInterface $user
	 * @return Profile
	 */
	protected function createProfile(SymfonyUserInterface $user) {
		$username = $user->getUsername();

		$this->logger->addDebug(sprintf('Request to create a new profile for "%s".', $user->getUsername()));

		$profile = new Profile();
		$profile->setUsername($username);
		if ($user instanceof LdapUser) {
			$profile->setName($user->getSurname());
			$profile->setFirstName($user->getGivenname());
		} else {
			$profile->setName($username);
			$profile->setFirstName($username);
		}

		$this->entityManager->persist($profile);
		$this->entityManager->flush();

		$this->logger->addInfo(sprintf('Created a new profile for "%s".', $user->getUsername()));

		return $profile;
	}

}