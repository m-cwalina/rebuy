<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["product:read"])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["product:read"])]

    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["product:read"])]

    private ?string $manufacturer = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(["product:read"])]

    private ?string $price = null;


    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: "product")]
    #[Groups(["product:read"])]

    private $categories;

    #[ORM\OneToMany(targetEntity: EANCode::class, mappedBy: "product")]
    #[Groups(["product:read"])]

    private $eanCodes;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->eanCodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            if ($category->getProduct() === $this) {
                $category->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EANCode[]
     */

    public function getEANCodes(): Collection
    {
        return $this->eanCodes;
    }

    public function addEANCode(EANCode $eanCode): self
    {
        if (!$this->eanCodes->contains($eanCode)) {
            $this->eanCodes[] = $eanCode;
            $eanCode->setProduct($this);
        }

        return $this;
    }

    public function removeEANCode(EANCode $eanCode): self
    {
        if ($this->eanCodes->removeElement($eanCode)) {
            if ($eanCode->getProduct() === $this) {
                $eanCode->setProduct(null);
            }
        }

        return $this;
    }
}
