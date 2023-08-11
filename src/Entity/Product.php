<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["category:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["category:read", "category:write"])]
    #[Assert\NotBlank(message: "Name of product cannot be blank.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["category:read", "category:write"])]
    #[Assert\NotBlank(message: "Manufacturer cannot be blank.")]
    private ?string $manufacturer = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(["category:read", "category:write"])]
    #[Assert\NotBlank(message: "Price cannot be blank.")]
    private ?string $price = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: "products", fetch: "EAGER", cascade: ['persist', 'remove'])]
    #[Groups(["product:read", "product:write"])]
    #[ORM\JoinTable(name: "product_category")]
    private $categories;

    #[ORM\OneToMany(targetEntity: EANCode::class, mappedBy: "product", fetch: "EAGER", cascade: ["persist", "remove", 'merge'])]
    #[Groups(["category:read", "category:write"])]
    #[Assert\Valid]
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
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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
        }

        return $this;
    }

    public function removeEANCode(EANCode $eanCode): self
    {
        $this->eanCodes->removeElement($eanCode);

        return $this;
    }
}
