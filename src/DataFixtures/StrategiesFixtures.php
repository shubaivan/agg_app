<?php


namespace App\DataFixtures;


use App\Cache\CacheManager;
use App\Entity\Strategies;
use App\Kernel;
use App\Services\ObjectsHandler;
use App\Services\StatisticsService;
use App\Util\RedisHelper;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

class StrategiesFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var ObjectsHandler
     */
    private $handler;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var
     */
    private $cs;

    /**
     * StrategiesFixtures constructor.
     * @param ObjectsHandler $handler
     * @param Kernel $kernel
     * @param EntityManager $em
     * @param SlugifyInterface $cs
     */
    public function __construct(
        ObjectsHandler $handler,
        KernelInterface $kernel,
        EntityManagerInterface $em,
        SlugifyInterface $cs
    )
    {
        $this->handler = $handler;
        $this->kernel = $kernel;
        $this->em = $em;

        $cs->addRule('&', 'and');
        $cs->addRule('->', '_own_');
        $this->cs = $cs;
    }


    public static function getGroups(): array
    {
        return ['my_pg_fixtures_strategies'];
    }

    /**
     * @param ObjectManager $manager
     * @throws \App\Exception\ValidatorException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function load(ObjectManager $manager)
    {
        $dir = $this->kernel->getProjectDir() . '/src/Services/Models/Shops/Strategies';
        $array_diff = array_diff(scandir($dir), array('..', '.'));
        foreach ($array_diff as $item) {
            $strategyName = preg_replace("/\..+/", "", $item);
            if (!is_dir($dir . '/' . $strategyName)) {
                $argument = 'App\Services\Models\Shops\Strategies\\' . $strategyName;
                $r = new \ReflectionClass($argument);
                $requireProperties = $r->newInstanceWithoutConstructor()::requireProperty();
                $value = [];
                foreach ($requireProperties as $property) {
                    $value[$property] = $r->getProperty($property)->getValue();
                }
                $value['strategyNameSpace'] = $argument;
                $value['strategyName'] = $strategyName;

                $objectRepository = $this->em->getRepository(Strategies::class);
                $oneBy = $objectRepository->findOneBy(['slug' => $this->cs->slugify($strategyName)]);
                if ($oneBy) {
                    $value['id'] = $oneBy->getId();
                }

                $handleObject = $this->handler->handleObject(
                    $value,
                    Strategies::class
                );
                $this->em->persist($handleObject);
            }
        }
        $this->em->flush();
    }
}