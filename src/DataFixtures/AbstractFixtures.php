<?php


namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryConfigurations;
use App\Entity\CategoryRelations;
use App\Entity\CategorySection;
use App\Kernel;
use App\Repository\CategoryRelationsRepository;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractFixtures extends Fixture
{
    private $manager;

    /**
     * @var integer
     */
    protected $minLen;

    /**
     * @var array
     */
    private $wordWithSpace = [];

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var bool
     */
    private $reUpdateFiles = false;

    private $swStopWords = '
        och
        det
        att
        i
        en
        jag
        hon
        som
        han
        på
        den
        med
        var
        sig
        för
        så
        till
        är
        men
        ett
        om
        hade
        de
        av
        icke
        mig
        du
        henne
        då
        sin
        nu
        har
        inte
        hans
        honom
        skulle
        hennes
        där
        min
        man
        ej
        vid
        kunde
        något
        från
        ut
        när
        efter
        upp
        vi
        dem
        vara
        vad
        över
        än
        dig
        kan
        sina
        här
        ha
        mot
        alla
        under
        någon
        eller
        allt
        mycket
        sedan
        ju
        denna
        själv
        detta
        åt
        utan
        varit
        hur
        ingen
        mitt
        ni
        bli
        blev
        oss
        din
        dessa
        några
        deras
        blir
        mina
        samma
        vilken
        er
        sådan
        vår
        blivit
        dess
        inom
        mellan
        sådant
        varför
        varje
        vilka
        ditt
        vem
        vilket
        sitta
        sådana
        vart
        dina
        vars
        vårt
        våra
        ert
        era
        vilkas
    ';

    /**
     * CategoryBarnFixtures constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param ObjectManager $manager
     * @param array $configurations
     * @param Category $main
     */
    protected function processConfiguration(array $configurations, Category $main): void
    {
        foreach ($configurations as $sectionName=>$section) {
            foreach ($section as $configuration) {
                $subMain = $this->createCategoryWithConf(
                    $configuration['name'],
                    $configuration['key_word'],
                    $sectionName,
                    $configuration['negative_key_words'] ?? null
                );

                $this->createCategoryRelations($main, $subMain);

                if (isset($configuration['sub_key_word'])
                    && is_array($configuration['sub_key_word'])
                    && count($configuration['sub_key_word']) > 0) {

                    $subKeyWords = $configuration['sub_key_word'];

                    $subKeyWordsArray = array_unique($subKeyWords);
                    foreach ($subKeyWordsArray as $key => $words) {
                        $words = preg_replace('/\s+/', '', $words);

                        $wordCategory = $this->createCategoryWithConf(
                            $key, $words
                        );

                        $this->createCategoryRelations($subMain, $wordCategory);
                    }
                }

                $this->getManager()->flush();
            }
        }



        $this->setDataInFile($this->kernel->getProjectDir() . '/pg/prepare_thesaurus_my_swedish.ths', PHP_EOL);
        $this->setDataInFile($this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths', PHP_EOL);
    }

    /**
     * @param string $sectionName
     * @return CategorySection
     */
    private function createdCategorySection(string $sectionName)
    {
        $categorySection = $this->getManager()
            ->getRepository(CategorySection::class)
            ->findOneBy(['sectionName' => $sectionName]);

        if(!$categorySection) {
            $categorySection = new CategorySection();
            $categorySection
                ->setSectionName($sectionName);
        }
        $this->getManager()->persist($categorySection);

        return $categorySection;
    }

    /**
     * @param string $categoryName
     * @param string $keyWords
     * @param string|null $negativeKeyWords
     * @param string|null $sectionName
     * @return Category
     */
    protected function createCategoryWithConf(
        string $categoryName,
        string $keyWords,
        ?string $sectionName = null,
        ?string $negativeKeyWords = null
    ): Category
    {
        $keyWords = $this->processingKeyWords($keyWords);
        $category = $this->checkExistCategory($categoryName);

        if (!$category instanceof Category) {
            $category = new Category();
            $category->setCategoryName($categoryName);
        }

        $category
            ->setCustomeCategory(true);
        if ($sectionName) {
            $section = $this->createdCategorySection($sectionName);
            $category->setSectionRelation($section);
        }

        $this->getManager()->persist($category);

        $categoryConfigurations = $category->getCategoryConfigurations();
        if (!$categoryConfigurations) {
            $categoryConfigurations = new CategoryConfigurations();
        }

        $categoryConfigurations
            ->setKeyWords($keyWords);
        if ($negativeKeyWords) {
//            $negativeKeyWords = preg_replace('/\s+/', '', $negativeKeyWords);

            $negativeKeyWords = preg_replace('/\n/', '', $negativeKeyWords);
            $negativeKeyWords = preg_replace('!\s+!', ' ', $negativeKeyWords);

            $negativeKeyWords = explode(', ', $negativeKeyWords);

            foreach ($negativeKeyWords as $key => $nword) {
                $nword = trim($nword);
                if (!strlen($nword)) {
                    unset($nword[$key]);
                }
                if (preg_match_all('!\s+!', $nword, $match)) {
                    $this->fillDictionary($nword);
                }
            }

            $nWords = array_unique($negativeKeyWords);
            $nWords = implode(',', $nWords);
            $categoryConfigurations
                ->setNegativeKeyWords($nWords);
        }
        $category->setCategoryConfigurations($categoryConfigurations);

        $this->getManager()->persist($categoryConfigurations);

        return $category;
    }

    /**
     * @param Category $m
     * @param Category $s
     * @return CategoryRelations
     */
    protected function createCategoryRelations(Category $m, Category $s)
    {
        /** @var CategoryRelationsRepository $objectRepository */
        $objectRepository = $this->getManager()->getRepository(CategoryRelations::class);
        $existCategoryRelations = $objectRepository
            ->findOneBy(['mainCategory' => $m, 'subCategory' => $s]);
        if ($existCategoryRelations instanceof CategoryRelations) {
            return $existCategoryRelations;
        }
        $categoryRelations = new CategoryRelations();
        $categoryRelations
            ->setMainCategory($m)
            ->setSubCategory($s);

        $this->getManager()->persist($categoryRelations);

        return $categoryRelations;
    }

    /**
     * @param string $categoryName
     * @return Category|object|null
     */
    protected function checkExistCategory(string $categoryName)
    {
        return $this->getCategoryRepository()
            ->findOneBy(['categoryName' => $categoryName]);
    }

    /**
     * @param string $keyWords
     * @param bool $withNewLine
     * @return string
     */
    protected function processingKeyWords(string $keyWords, bool $withNewLine = false): string
    {
        //        $keyWords = preg_replace('/\s+/', '', $keyWords);
        $keyWords = trim($keyWords);
        $keyWords = trim($keyWords, ',');
        $keyWords = preg_replace('/\n/', '', $keyWords);
        $keyWords = preg_replace('!\s+!', ' ', $keyWords);

        $words = explode(', ', $keyWords);

        foreach ($words as $key => $word) {
            $word = trim($word);
            $strlen = strlen($word);

            if (!strlen($word)) {
                unset($words[$key]);
            }
            if (preg_match_all('!\s+!', $word, $match)) {
                $this->fillDictionary($word);
            } else {
                if (!$this->minLen || $strlen < $this->minLen) {
                    $this->minLen = $strlen;
                }
            }
        }
        $words = array_unique($words);

        $words = array_filter($words, function ($v) {
            if (strlen(trim($v))) {
                return true;
            }
        });

        $keyWords = implode(',', $words);
        if ($withNewLine) {
            $this->setDataInFile($this->kernel->getProjectDir() . '/pg/prepare_thesaurus_my_swedish.ths', PHP_EOL);
            $this->setDataInFile($this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths', PHP_EOL);
        }
        return $keyWords;
    }

    /**
     * @param string $word
     */
    private function fillDictionary(string $word)
    {
        $this->wordWithSpace[] = $word;

        if (!is_array($this->swStopWords)) {
            $explode = explode(PHP_EOL, $this->swStopWords);

            $arrayFilter = array_filter($explode, function ($v) {
                if (strlen(trim($v))) {
                    return true;
                }
            });

            $arrayMap = array_map(function ($v) {
                return trim($v);
            }, $arrayFilter);

            $arrayUnique = array_unique($arrayMap);
            $prepareStopArray = [];
            foreach ($arrayUnique as $uniq) {
                $prepareStopArray[] = ucfirst($uniq);
                $prepareStopArray[] = lcfirst($uniq);
            }

            $prepareStopArray = array_map(function ($v) {
                return '\b' . trim($v) . '\b';
            }, $prepareStopArray);

            $this->swStopWords = $prepareStopArray;
        }

        $prepareRegex = implode('|', $this->swStopWords);

        if (preg_match_all("/$prepareRegex/u", $word, $mt)) {
            $result = preg_replace("/$prepareRegex/u", '?', $word);
        }

        $modifyIndexWord = str_replace(' ', '', $word);

        $this->setDataInFile(
            $this->kernel->getProjectDir() . '/pg/prepare_thesaurus_my_swedish.ths',
            (count($this->wordWithSpace) == 1 ? '' : PHP_EOL) . (isset($result) ? $result : $word) . ' : ' . $modifyIndexWord
        );

        $this->setDataInFile(
            $this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths',
            (count($this->wordWithSpace) == 1 ? '' : PHP_EOL) . $word . ' : ' . $modifyIndexWord
        );
    }

    protected function reUpdateFiles()
    {
        if (!$this->reUpdateFiles) {
            if (!is_dir($this->kernel->getProjectDir() . '/pg')) {
                mkdir($this->kernel->getProjectDir() . '/pg');
            }

            if (file_exists($this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths')) {
                unlink($this->kernel->getProjectDir() . '/pg/thesaurus_my_swedish.ths');
            }
            if (file_exists($this->kernel->getProjectDir() . '/pg/prepare_thesaurus_my_swedish.ths')) {
                unlink($this->kernel->getProjectDir() . '/pg/prepare_thesaurus_my_swedish.ths');
            }
            $this->reUpdateFiles = true;
        }
    }

    protected function afterLoad()
    {
        echo static::class . ' : min word length: ' . $this->minLen . PHP_EOL;
    }


    /**
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     * @return AbstractFixtures
     */
    protected function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository|CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getManager()->getRepository(Category::class);
    }

    /**
     * @param string $path
     * @param string $data
     */
    private function setDataInFile(string $path, string $data): void
    {
        file_put_contents(
            $path,
            $data,
            FILE_APPEND
        );
    }
}