<?php

namespace App\DataFixtures;

use App\Entity\References as Ref;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

use function mt_rand;

class ReferencesFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct()
    {
    }

    /**
     * @see FixtureGroupInterface
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['group_all', 'group_app', 'group_references'];
    }

    /**
     * @see \Doctrine\Common\DataFixtures\FixtureInterface
     */
    public function load(ObjectManager $manager): void
    {
        /**
         * City [Город]
         */
        $cities = [];
        for ($i = 0; $i < 20; $i++) {
            $city = new Ref\City(name: 'Город ' . $i);
            $manager->persist($city);
            $cities[$i] = $city;
        }

        /**
         * Storage [Склад]
         */
        for ($i = 0; $i < 20; $i++) {
            $storage = new Ref\Storage(name: 'Склад ' . $i, city: $cities[mt_rand(0, 19)]);
            $manager->persist($storage);
        }

        $manager->flush();
    }
}
