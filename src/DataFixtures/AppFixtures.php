<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\EANCode;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setCategory('Electronics');
        $manager->persist($category);

        $product = new Product();
        $product->setName('Smartphone');
        $product->setPrice(132.10);
        $product->setManufacturer('Apple');
        $product->addCategory($category);
        $manager->persist($product);

        $eanCode = new EANCode();
        $eanCode->setCode('1234567890123');
        $eanCode->setProduct($product);
        $manager->persist($eanCode);

        $manager->flush();
    }
}
