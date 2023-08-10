<?php

namespace App\Entity;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]

 #[ApiResource()]

class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["category:read", 'category:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Category cannot be blank.")]
    #[Groups(["category:read", "category:write"])]
    private ?string $category = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: "categories", fetch: "EAGER", cascade: ['persist'])]
    #[Groups(["category:read", "category:write"])]
    #[Assert\Valid]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection|Product[]
     */

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
      if (!$this->products->contains($product)) {
          $this->products[] = $product;
          $product->addCategory($this);
      }

      return $this;
    }

    public function removeProduct(Product $product): self
    {
      if ($this->products->contains($product)) {
          $this->products->removeElement($product);
          $product->removeCategory($this);
      }

      return $this;
    }
}
