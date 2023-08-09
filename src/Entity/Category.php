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

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["product:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["product:read"])]
    private ?string $category = null;


     #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: "categories")]
     #[Groups(["product:read"])]

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

    public function setCategory(string $category): self
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
}
