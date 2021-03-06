<?php


namespace Mealz\MealBundle\Twig\Extension;

use Mealz\MealBundle\Entity\Meal;
use Mealz\MealBundle\Service\Doorman as DoormanService;

class Doorman extends \Twig_Extension {

	/**
	 * @var DoormanService
	 */
	protected $doormanService;

	public function __construct(DoormanService $doormanService) {
		$this->doormanService = $doormanService;
	}

	public function getFunctions() {
		return array(
			'is_allowed_to_join' => new \Twig_Function_Method($this, 'isUserAllowedToJoin'),
			'is_allowed_to_leave' => new \Twig_Function_Method($this, 'isUserAllowedToLeave'),
			'is_allowed_to_add_guest' => new \Twig_Function_Method($this, 'isUserAllowedToAddGuest'),
			'is_allowed_to_remove_guest' => new \Twig_Function_Method($this, 'isUserAllowedToRemoveGuest'),
		);
	}

	public function isUserAllowedToJoin(Meal $meal) {
		// proxy method
		return $this->doormanService->isUserAllowedToJoin($meal);
	}

	public function isUserAllowedToLeave(Meal $meal) {
		// proxy method
		return $this->doormanService->isUserAllowedToLeave($meal);
	}

	public function isUserAllowedToAddGuest(Meal $meal) {
		// proxy method
		return $this->doormanService->isUserAllowedToAddGuest($meal);
	}

	public function isUserAllowedToRemoveGuest(Meal $meal) {
		// proxy method
		return $this->doormanService->isUserAllowedToRemoveGuest($meal);
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName() {
		return 'doorman';
	}
}