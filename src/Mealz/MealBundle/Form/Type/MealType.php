<?php

namespace Mealz\MealBundle\Form\Type;

use Mealz\MealBundle\Entity\Day;
use Mealz\MealBundle\Entity\Dish;
use Mealz\MealBundle\Entity\DishRepository;
use Mealz\MealBundle\Entity\Meal;
use Mealz\MealBundle\Entity\Week;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MealType extends AbstractType
{
    protected $dishRepository;

    public function __construct(DishRepository $dishRepository)
    {
        $this->dishRepository = $dishRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dish', EntityType::class, array(
            'class' => 'MealzMealBundle:Dish',
            'query_builder' => $this->dishRepository->getSortedDishesQueryBuilder(array(
                'load_disabled' => true
            )),
            'required' => false,
            'group_by' => function(Dish $dish) {
                return ($dish->isEnabled() && $category = $dish->getCategory()) ? $category : null;
            },
            'choice_attr' => function (Dish $dish, $value, $index) {
                return ($dish->isEnabled()) ? [] : ['disabled' => 'disabled'];
            },
            'choice_translation_domain' => 'general'
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($builder) {
            /** @var Meal $meal */
            $meal = $event->getData();
            /** @var Day $day */
            $day = $meal->getDay();
            /** @var Week $week */
            $week = $day->getWeek();

            if (false === $day->isEnabled() || false === $week->isEnabled()) {
                $form = $event->getForm();
                $config = $form->get('dish')->getConfig();
                $options = $config->getOptions();

                $form->add(
                    'dish',
                    $config->getType()->getName(),
                    array_replace(
                        $options,
                        [
                            'disabled' => true
                        ]
                    )
                );
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($builder) {
            /** @var Meal $meal */
            $meal = $event->getData();
            if (null !== $meal->getDish()) {
                $dishPrice = $meal->getDish()->getPrice();
                $meal->setPrice($dishPrice);
                $event->setData($meal);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mealz\MealBundle\Entity\Meal',
            'error_bubbling' => false
        ));
    }

}