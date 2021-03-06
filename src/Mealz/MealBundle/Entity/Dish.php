<?php

namespace Mealz\MealBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Dish
 *
 * @ORM\Table(name="dish")
 * @ORM\Entity(repositoryClass="DishRepository")
 */
class Dish
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Gedmo\Slug(fields={"title_en"})
	 * @ORM\Column(length=128, unique=true)
	 * @var string
	 */
	protected $slug;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 * @ORM\Column(type="string", length=255, nullable=FALSE)
	 * @var string
	 */
	protected $title_en;

	/**
	 * @Assert\Length(max=4096)
	 * @ORM\Column(type="text", nullable=TRUE)
	 * @var null|string
	 */
	protected $description_en = NULL;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 * @ORM\Column(type="string", length=255, nullable=FALSE)
	 * @var string
	 */
	protected $title_de;

	/**
	 * @Assert\Length(max=4096)
	 * @ORM\Column(type="text", nullable=TRUE)
	 * @var null|string
	 */
	protected $description_de = NULL;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="dishes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     * @var null|Category
     */
    protected $category = NULL;

	/**
	 * @ORM\Column(type="decimal", precision=10, scale=4, nullable=TRUE)
	 * @var null|float
	 */
	protected $price = NULL;

	/**
	 * @ORM\Column(type="boolean", nullable=FALSE)
	 * @var bool
	 */
	protected $enabled = TRUE;

	/**
	 * @var string
	 */
	protected $currentLocale = 'en';

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @deprecated use setDescriptionEn() instead
	 * @param null|string $description
	 */
	public function setDescription($description)
	{
		$this->setDescriptionEn($description);
	}

	/**
	 * @return null|string
	 */
	public function getDescription()
	{
		if($this->currentLocale == 'de' && $this->description_de) {
			return $this->getDescriptionDe();
		} else {
			return $this->getDescriptionEn();
		}
	}

	/**
	 * @param float|null $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return float|null
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param string $title
	 * @deprecated use setTitleEn() or setTitleDe() instead
	 */
	public function setTitle($title)
	{
		$this->setTitleEn($title);
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		if($this->currentLocale == 'de' && $this->title_de) {
			return $this->getTitleDe();
		} else {
			return $this->getTitleEn();
		}
	}

	/**
	 * @param boolean $enabled
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @param string $currentLocale
	 */
	public function setCurrentLocale($currentLocale)
	{
		$this->currentLocale = $currentLocale;
	}

	/**
	 * @return string
	 */
	public function getCurrentLocale()
	{
		return $this->currentLocale;
	}

	/**
	 * @param null|string $description_de
	 */
	public function setDescriptionDe($description_de)
	{
		$this->description_de = $description_de;
	}

	/**
	 * @return null|string
	 */
	public function getDescriptionDe()
	{
		return $this->description_de;
	}

	/**
	 * @param null|string $description_en
	 */
	public function setDescriptionEn($description_en)
	{
		$this->description_en = $description_en;
	}

	/**
	 * @return null|string
	 */
	public function getDescriptionEn()
	{
		return $this->description_en;
	}

	/**
	 * @param string $title_de
	 */
	public function setTitleDe($title_de)
	{
		$this->title_de = $title_de;
	}

	/**
	 * @return string
	 */
	public function getTitleDe()
	{
		return $this->title_de;
	}

	/**
	 * @param string $title_en
	 */
	public function setTitleEn($title_en)
	{
		$this->title_en = $title_en;
	}

	/**
	 * @return string
	 */
	public function getTitleEn()
	{
		return $this->title_en;
	}

	public function __toString() {
		return $this->getTitle();
	}

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }
}
