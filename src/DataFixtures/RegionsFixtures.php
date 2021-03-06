<?php
/**
 * Created by PhpStorm.
 * User: blerimi_v
 * Date: 1/19/2019
 * Time: 9:16 AM
 */

namespace App\DataFixtures;


use App\Entity\Regions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RegionsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $region = new Regions();
        $region->setName('Prishtinë');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Mitrovicë');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Gjilan');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Prizren');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Ferizaj');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Pejë');
        $manager->persist($region);
        $manager->flush();

        $region = new Regions();
        $region->setName('Gjakovë');
        $manager->persist($region);
        $manager->flush();
    }
}
