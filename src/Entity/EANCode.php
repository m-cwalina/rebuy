<?php

namespace App\Entity;
use App\Repository\EANCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EANCodeRepository::class)]
class EANCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["category:read", "category:write"])]
    #[Assert\NotBlank(message: "Code cannot be blank.")]
    #[Assert\Length(exactly: 13)]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "eanCodes", cascade: ["persist", "remove", 'merge'])]
    #[Groups(["category:read", "category:write"])]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }
}
