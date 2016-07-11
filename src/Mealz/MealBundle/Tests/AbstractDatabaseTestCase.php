<?php


namespace Mealz\MealBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Mealz\MealBundle\Entity\Category;
use Mealz\MealBundle\Entity\Dish;
use Mealz\MealBundle\Entity\Meal;
use Mealz\UserBundle\Entity\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractDatabaseTestCase extends WebTestCase {

	public function setUp() {
		parent::setUp();
		static::$kernel = static::createKernel();
		static::$kernel->boot();
	}

	/**
	 * empty the test database and load fixtures from a class
	 *
	 * @param FixtureInterface|array|null $fixtures
	 * @throws \InvalidArgumentException
	 */
	protected function loadFixtures($fixtures) {
		$em = static::$kernel->getContainer()->get('doctrine')->getManager();
		$loader = new Loader();
		if(is_array($fixtures) || $fixtures instanceof \Iterator) {
			foreach($fixtures as $fixture) {
				$loader->addFixture($fixture);
			}
		} elseif ($fixtures instanceof FixtureInterface) {
			$loader->addFixture($fixtures);
		} elseif($fixtures === NULL) {
			// nothing to do
		} else {
			throw new \InvalidArgumentException(sprintf(
				'%s expects first parameter to be a FixtureInterface or array. %s given.',
				__METHOD__,
				get_class($fixtures)
			));
		}
		$purger = new ORMPurger($em);
		$executor = new ORMExecutor($em, $purger);
		$executor->execute($loader->getFixtures());
	}

	protected function clearAllTables() {
		$this->loadFixtures(NULL);
	}

	/**
	 * @return Registry
	 */
	protected function getDoctrine() {
		return static::$kernel->getContainer()->get('doctrine');
	}

	/**
	 * @param Category $category
	 * @return Dish
	 */
	protected function createDish(Category $category = null) {
		$dish = new Dish();
		$dish->setTitleEn('Test EN ' . rand());
		$dish->setTitleDe('Test DE ' . rand());
		if ($category) {
			$dish->setCategory($category);
		}

		return $dish;
	}

	/**
	 * @param \Mealz\MealBundle\Entity\Dish $dish
	 * @return Meal
	 */
	protected function createMeal(Dish $dish = NULL) {
		$meal = new Meal();
		$meal->setDish($dish ?: $this->createDish());
		$meal->setDateTime(new \DateTime());
		$meal->setPrice(1.23);

		return $meal;
	}

	/**
	 * @return Profile
	 */
	protected function createProfile() {
		$profile = new Profile();
		$profile->setUsername('Test ' . rand());

		return $profile;
	}

	/**
	 * @return Category
	 */
	protected function createCategory()
	{
		$category = new Category();
		$category->setTitleDe('Title DE ' . rand());
		$category->setTitleEn('Title EN ' . rand());

		return $category;
	}

	/**
	 * @param array $entities
	 */
	public function persistAndFlushAll($entities) {
		$em = $this->getDoctrine()->getManager();

		$em->transactional(function($em) use ($entities) {
			/** @var EntityManager $em */
			// transaction is need for Participant entities
			foreach($entities as $entity) {
				$em->persist($entity);
			}
			$em->flush();
		});
	}
}