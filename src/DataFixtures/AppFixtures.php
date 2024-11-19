<?php

namespace App\DataFixtures;

use App\Entity\Bay;
use App\Entity\BillingType;
use App\Entity\Discount;
use App\Entity\Offer;
use App\Entity\Unit;
use App\Entity\UnitUsage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $billingTypes = [
            ['months' => 1, 'discountOverMonthly' => 0],
            ['months' => 3, 'discountOverMonthly' => 5],
            ['months' => 6, 'discountOverMonthly' => 10],
            ['months' => 12, 'discountOverMonthly' => 20],
        ];
        $billingTypeEntities = [];
        foreach ($billingTypes as $billingType) {
            $entity = new BillingType();
            $entity->setMonths($billingType['months']);
            $entity->setDiscountOverMonthly($billingType['discountOverMonthly']);
            $billingTypeEntities[] = $entity;
        }

        $offers = [
            ['name' => 'Base', 'maxUnits' => 1, 'availability' => '99.999%', 'monthlyRentPrice' => 99.99, 'bandwidth' => '100 Mbps', 'isActive' => true],
            ['name' => 'Start-up', 'maxUnits' => 10, 'availability' => '99.999%', 'monthlyRentPrice' => 899.99, 'bandwidth' => '200 Mbps', 'isActive' => true],
            ['name' => 'PME', 'maxUnits' => 21, 'availability' => '99.999%', 'monthlyRentPrice' => 1679.99, 'bandwidth' => '250 Mbps', 'isActive' => true],
            ['name' => 'Enterprise', 'maxUnits' => 42, 'availability' => '99.999%', 'monthlyRentPrice' => 2949.99, 'bandwidth' => '500 Mbps', 'isActive' => true],
        ];
        $offerEntities = [];
        foreach ($offers as $offer) {
            $entity = new Offer();
            $entity->setName($offer['name']);
            $entity->setMaxUnits($offer['maxUnits']);
            $entity->setAvailability($offer['availability']);
            $entity->setMonthlyRentPrice($offer['monthlyRentPrice']);
            $entity->setBandwidth($offer['bandwidth']);
            $entity->setActive($offer['isActive']);
            $offerEntities[] = $entity;
        }

        $bays = [
            ['name' => 'B001', 'location' => 'Europe Centrale'],
            ['name' => 'B002', 'location' => 'Europe Centrale'],
            ['name' => 'B003', 'location' => 'Europe Centrale'],
            ['name' => 'B004', 'location' => 'Europe Centrale'],
            ['name' => 'B005', 'location' => 'Europe Centrale'],
            ['name' => 'B006', 'location' => 'Europe Centrale'],
            ['name' => 'B007', 'location' => 'Europe Centrale'],
            ['name' => 'B008', 'location' => 'Europe Centrale'],
            ['name' => 'B009', 'location' => 'Europe Centrale'],
            ['name' => 'B010', 'location' => 'Europe Centrale'],
            ['name' => 'B011', 'location' => 'USA Ouest'],
            ['name' => 'B012', 'location' => 'USA Ouest'],
            ['name' => 'B013', 'location' => 'USA Ouest'],
            ['name' => 'B014', 'location' => 'USA Ouest'],
            ['name' => 'B015', 'location' => 'USA Ouest'],
            ['name' => 'B016', 'location' => 'USA Ouest'],
            ['name' => 'B017', 'location' => 'USA Ouest'],
            ['name' => 'B018', 'location' => 'USA Ouest'],
            ['name' => 'B019', 'location' => 'USA Ouest'],
            ['name' => 'B020', 'location' => 'USA Ouest'],
            ['name' => 'B021', 'location' => 'Asie'],
            ['name' => 'B022', 'location' => 'Asie'],
            ['name' => 'B023', 'location' => 'Asie'],
            ['name' => 'B024', 'location' => 'Asie'],
            ['name' => 'B025', 'location' => 'Asie'],
            ['name' => 'B026', 'location' => 'Asie'],
            ['name' => 'B027', 'location' => 'Asie'],
            ['name' => 'B028', 'location' => 'Asie'],
            ['name' => 'B029', 'location' => 'Asie'],
            ['name' => 'B030', 'location' => 'Asie'],
        ];
        $baysEntities = [];
        foreach ($bays as $bay) {
            $entity = new Bay();
            $entity->setName($bay['name']);
            $entity->setLocation($bay['location']);
            $baysEntities[] = $entity;
        }

        $units = [
            ['name' => 'U001'],
            ['name' => 'U002'],
            ['name' => 'U003'],
            ['name' => 'U004'],
            ['name' => 'U005'],
            ['name' => 'U006'],
            ['name' => 'U007'],
            ['name' => 'U008'],
            ['name' => 'U009'],
            ['name' => 'U010'],
            ['name' => 'U011'],
            ['name' => 'U012'],
            ['name' => 'U013'],
            ['name' => 'U014'],
            ['name' => 'U015'],
            ['name' => 'U016'],
            ['name' => 'U017'],
            ['name' => 'U018'],
            ['name' => 'U019'],
            ['name' => 'U020'],
            ['name' => 'U021'],
            ['name' => 'U022'],
            ['name' => 'U023'],
            ['name' => 'U024'],
            ['name' => 'U025'],
            ['name' => 'U026'],
            ['name' => 'U027'],
            ['name' => 'U028'],
            ['name' => 'U029'],
            ['name' => 'U030'],
            ['name' => 'U031'],
            ['name' => 'U032'],
            ['name' => 'U033'],
            ['name' => 'U034'],
            ['name' => 'U035'],
            ['name' => 'U036'],
            ['name' => 'U037'],
            ['name' => 'U038'],
            ['name' => 'U039'],
            ['name' => 'U040'],
            ['name' => 'U041'],
            ['name' => 'U042'],
        ];
        $unitEntities = [];
        foreach ($baysEntities as $bay) {
            foreach ($units as $unit) {
                $entity = new Unit();
                $entity->setName($unit['name']);
                $entity->setBay($bay);
                $unitEntities[] = $entity;
            }
        }

        $unitUsages = [
            ['name' => 'Serveur web', 'color' => '#ff0000'],
            ['name' => 'Serveur de sauvegarde', 'color' => '#00ff00'],
            ['name' => 'Serveur de base de données', 'color' => '#0000ff'],
            ['name' => 'Archive', 'color' => '#ffff00'],
            ['name' => 'Serveur de noms de domaine', 'color' => '#ff00ff'],
            ['name' => 'Messagerie', 'color' => '#00ffff'],
            ['name' => 'Développement', 'color' => '#000000'],
            ['name' => 'Test', 'color' => '#ffffff'],
            ['name' => 'Autre', 'color' => '#222222'],
        ];
        $unitUsageEntities = [];
        foreach ($unitUsages as $unitUsage) {
            $entity = new UnitUsage();
            $entity->setName($unitUsage['name']);
            $entity->setColor($unitUsage['color']);
            $unitUsageEntities[] = $entity;
        }

        $discounts = [
            ['code' => 'bonjour', 'amount' => 10, 'isPercentage' => true, 'isActive' => true],
            ['code' => 'gentil10', 'amount' => 10, 'isPercentage' => false, 'isActive' => true],
            ['code' => 'welcome2024', 'amount' => 5, 'isPercentage' => true, 'isActive' => false],
        ];
        $discountEntities = [];
        foreach ($discounts as $discount) {
            $entity = new Discount();
            $entity->setCode($discount['code']);
            $entity->setAmount($discount['amount']);
            $entity->setPercentage($discount['isPercentage']);
            $entity->setActive($discount['isActive']);
            $discountEntities[] = $entity;
        }

        $admin = new User();
        $admin->setEmail('admin@worktogether.biz');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));

        foreach ($billingTypeEntities as $billingTypeEntity) {
            $manager->persist($billingTypeEntity);
        }
        foreach ($offerEntities as $offerEntity) {
            $manager->persist($offerEntity);
        }
        foreach ($baysEntities as $bayEntity) {
            $manager->persist($bayEntity);
        }
        foreach ($unitEntities as $unitEntity) {
            $manager->persist($unitEntity);
        }
        foreach ($unitUsageEntities as $unitUsageEntity) {
            $manager->persist($unitUsageEntity);
        }
        foreach ($discountEntities as $discountEntity) {
            $manager->persist($discountEntity);
        }
        $manager->persist($admin);

        $manager->flush();
    }
}
