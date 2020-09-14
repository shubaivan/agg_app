<?php


namespace App\DataFixtures;


use App\Entity\AdminConfiguration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdminConfigurationFixtures extends AbstractFixtures implements DependentFixtureInterface
{
    const PROPERTYDATA_BRAND = '
        Rosewall, Mavala, Something Borrowed, Woodbird, Ahlvar Gallery, Scampi, Jascha Stockholm, Milook, BareMinerals, 
        Yves Saint Laurent, Mcdodo, Cavaliere, Max Factor, Sir Of Sweden, Dior, Oakwood, Cavaliere, 
        Anastasia Beverly Hills, IsaDora, Clarins For Men, CHPO, Clinique For Men, Lexington, Ronneby Bruk, 
        Jumperfabriken, Woodbird, ARTDECO, Jim Rickey, Elvine, WeSC, Nikolaj D"\'Étoiles, Pearl Izumi, Vaude, 
        Karen By Simonsen, Inwear, Filippa K, By Malene Birger, Soaked In Luxury, American Vintage, Maria Westerlind, 
        Carin Wester, 2NDDAY, Whyred, Twist & Tango,  French Connection, Zizzi, By Malene Birger, Rodebjer, Ivyrevel, 
        Gerry Weber, Fransa, Esprit, Boomerang, Rosemunde, PRODUKT, Soyaconcept, Stylein, Munthe, Calvin Klein, Casall, 
        Baum Und Pferdgarten, Sibin Linnebjerg, tommy hilfiger, Levis, Aubade, FEMILET, Under Armour, Salming, 
        Matinique, ICHI, J.Lindeberg, Tiger Of Sweden, Marville Road, Part Two, DAY Birger Et Mikkelsen, 
        Selected Homme, Blankens, Calida, Vagabond, Royal Republiq, Reschia, Wera, Sofie Schnoor, Steve Madden, Eytys, 
        Blankens';

    const PROPERTYDATA_KEYWORDS = '
               vuxen, fucker, sex, adult, woman, Dam, Herr
            ';

    public function load(ObjectManager $manager)
    {
        $processingKeyWordsBrand = $this->processingKeyWords(
            self::PROPERTYDATA_BRAND, false, false
        );
        $adminConfigurationBrand = new AdminConfiguration();
        $adminConfigurationBrand
            ->setPropertyName(AdminConfiguration::GLOBAL_NEGATIVE_BRAND_KEY_WORDS)
            ->setPropertyData($processingKeyWordsBrand);
        $manager->persist($adminConfigurationBrand);

        $processingKeyWords = $this->processingKeyWords(self::PROPERTYDATA_KEYWORDS);
        $adminConfigurationGlobal = new AdminConfiguration();
        $adminConfigurationGlobal
            ->setPropertyName(AdminConfiguration::GLOBAL_NEGATIVE_KEY_WORDS)
            ->setPropertyData($processingKeyWords);
        $manager->persist($adminConfigurationGlobal);

        $manager->flush();

        $this->afterLoad();
    }

    public function getDependencies()
    {
        return array(
            CategoryInredningAndSakerhetFixtures::class,
        );
    }
}