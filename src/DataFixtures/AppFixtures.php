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
        $category1 = new Category();
        $category1->setCategory('Electronics');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setCategory('Apparel');
        $manager->persist($category2);

        $product1 = new Product();
        $product1->setName('iPhone 14')
                 ->setManufacturer('Apple')
                 ->setPrice('999.99')
                 ->addCategory($category1);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('T-Shirt')
                 ->setManufacturer('Uniqlo')
                 ->setPrice('29.99')
                 ->addCategory($category2);
        $manager->persist($product2);

        $ean1 = new EANCode();
        $ean1->setCode('1234567890123')
             ->setProduct($product1);
        $manager->persist($ean1);

        $ean2 = new EANCode();
        $ean2->setCode('2345678901234')
             ->setProduct($product2);
        $manager->persist($ean2);

        $manager->flush();
    }

}
