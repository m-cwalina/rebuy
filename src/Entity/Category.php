<?php

// namespace App\Entity;

// use App\Repository\CategoryRepository;
// use Doctrine\ORM\Mapping as ORM;
// use Symfony\Component\Serializer\Annotation\Groups;

// #[ORM\Entity(repositoryClass: CategoryRepository::class)]
// class Category
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     #[Groups(["product:read"])]
//     private ?int $id = null;

//     #[ORM\Column(length: 255)]
//     #[Groups(["product:read"])]
//     private ?string $category = null;

//     #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "categories", fetch: "EAGER")]
//     #[ORM\JoinColumn(nullable: false)]
//     private $product;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getCategory(): ?string
//     {
//         return $this->category;
//     }

//     public function setCategory(string $category): static
//     {
//         $this->category = $category;
//         return $this;
//     }

//     public function getProduct(): ?Product
//     {
//         return $this->product;
//     }

//     public function setProduct(?Product $product): self
//     {
//         $this->product = $product;
//         return $this;
//     }
// }

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

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
    #[Groups(["category:read", "category:write"])]
    private ?string $category = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: "categories", fetch: "EAGER", cascade: ['persist'])]
    #[Groups(["category:read", "category:write"])]
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
