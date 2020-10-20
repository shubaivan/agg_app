<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 * @ORM\Table(name="shop",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="shop_name_idx", columns={"shop_name"}),
 *        @UniqueConstraint(name="shop_slug_idx", columns={"slug"}),
 *    }
 * )
 * @UniqueEntity(fields={"shopName"})
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="shops_region")
 * @ORM\HasLifecycleCallbacks()
 */
class Shop extends SEOModel implements AttachmentFilesInterface
{
    const PREFIX_HASH = 'statistic:';

    const PREFIX_HANDLE_ANALYSIS_PRODUCT_SUCCESSFUL = 'shop:processing:analysis_product_successful:';
    const PREFIX_HANDLE_ANALYSIS_PRODUCT_EXIST = 'shop:processing:analysis_product_exist:';
    const PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL_NEW_ONE = 'shop:processing:successful_new_one:';
    const PREFIX_PROCESSING_DATA_SHOP_SUCCESSFUL_EXIST = 'shop:processing:successful_exist:';
    const PREFIX_PROCESSING_DATA_SHOP_FAILED = 'shop:processing:failed:';
    const PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION_BRAND = 'shop:processing:global_match_exception_brand:';
    const PREFIX_PROCESSING_DATA_SHOP_GLOBAL_MATCH_EXCEPTION = 'shop:processing:global_match_exception:';
    const PREFIX_PROCESSING_DATA_SHOP_ADMIN_SHOP_RULES_EXCEPTION = 'shop:processing:admin_shop_rules_exception:';

    const PREFIX_HANDLE_MATCH_BY_IDENTITY_BY_UNIQ_DATA = 'shop:handle:match_by_identityUniqData:';
    const PREFIX_HANDLE_NEW_ONE = 'shop:handle:new_one:';
    
    const PREFIX_PROCESSING_MATCH_BY_IDENTITY_BY_UNIQ_DATA = 'shop:processing:match_by_identityUniqData:';

    const PREFIX_HANDLE_DATA_SHOP_SUCCESSFUL = 'shop:handle:successful:';
    const PREFIX_HANDLE_DATA_SHOP_FAILED = 'shop:handle:failed:';

    const SERIALIZED_GROUP_LIST = 'shop_group_list';
    const ADRECORD = 'Adrecord';
    const ADTRACTION = 'Adtraction';
    const AWIN = 'Awin';
    const TRADE_DOUBLER = 'TradeDoubler';

    private static $shopNamesAdtractionMapping = [
        'babyland' => 'Babyland',
        'babyV' => 'BabyV',
        'elodi' => 'Elodi',
        'lindex' => 'Lindex',
        'ahlens' => 'Åhlens',
        'cykloteket' => 'Cykloteket',
        'cos' => 'COS',
        'bjorn_borg' => 'Björn Borg',
        'lekia' => 'Lekia',
        'litenleker' => 'Litenleker',
        'sneakersPoint' => 'SneakersPoint',
        'stor_and_liten' => 'Stor & Liten',
        'polarn_pyret' => 'Polarn O. Pyret',
        'adlibris' => 'Adlibris',
        'outdoorexperten' => 'Outdoorexperten'
    ];

    private static $shopNamesAdrecordMapping = [
        'baby_bjorn' => 'Baby Björn',
        'cardoonia' => 'Cardoonia',
        'ebbeKids' => 'EbbeKids',
        'frankDandy' => 'FrankDandy',
        'gus_textil' => 'Gus Textil',
        'jultroja' => 'Jultröja',
        'leksakscity' => 'Leksakscity',
        'nalleriet' => 'Nalleriet',
        'namnband' => 'Namnband',
        'shirtstore' => 'Shirtstore',
        'spelexperten' => 'Spelexperten',
        'sportshopen' => 'Sportshopen',
        'stigaSports' => 'StigaSports',
        'twar' => 'Twar',
    ];

    private static $shopNamesAwinMapping = [
        'vegaoo' => 'Vegaoo',
        'nike' => 'Nike',
        'nordic_nest' => 'Nordic Nest',
        'nepiece_nordic' => 'Onepiece Nordic',
        'blue_tomato' => 'Blue Tomato',
        'ellos_se' => 'Ellos SE',
        'jd_sports' => 'JD Sports',
        'cubus' => 'Cubus',
    ];

    private static $shopNamesTradeDoublerMapping = [
        'sportamore' => 'Sportamore',
        'bonprix' => 'Bonprix',
        'cdon_barn_and_baby' => 'CDON Barn and Baby',
        'cdon_shoes' => 'CDON Shoes',
        'geggamoja' => 'Geggamoja',
        'gina_tricot' => 'Gina Tricot',
        'eskor' => 'Eskor',
        'pinkorblue' => 'Pinkorblue',
        'boozt' => 'Boozt',
        'desigual' => 'Desigual',
        'coolshop' => 'Coolshop',
        'teddymania' => 'Teddymania'
    ];

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST, Product::SERIALIZED_GROUP_LIST})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Annotation\Groups({Shop::SERIALIZED_GROUP_LIST})
     */
    private $shopName;

    /**
     * @var Collection|Product[]
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     * @ORM\OneToMany(targetEntity="Product", mappedBy="shopRelation", fetch="LAZY")
     */
    private $products;

    /**
     * @var Files
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Files",
     *     mappedBy="shop",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     * @Assert\Valid()
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="shops_region")
     */
    private $files;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(string $shopName): self
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        if (!$this->products) {
            $this->products = new ArrayCollection();
        }

        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->getProducts()->contains($product)) {
            $this->products[] = $product;
            $product->setShopRelation($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->getProducts()->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getShopRelation() === $this) {
                $product->setShopRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getShopNamesMapping(): array
    {
        return array_merge(
            self::$shopNamesAdrecordMapping,
            self::$shopNamesAdtractionMapping,
            self::$shopNamesAwinMapping,
            self::$shopNamesTradeDoublerMapping
        );
    }

    /**
     * @return array
     */
    public static function getGroupShopNamesMapping(): array
    {
        return array_merge(
            [self::ADRECORD => self::$shopNamesAdrecordMapping],
            [self::ADTRACTION => self::$shopNamesAdtractionMapping],
            [self::AWIN => self::$shopNamesAwinMapping],
            [self::TRADE_DOUBLER => self::$shopNamesTradeDoublerMapping]
        );
    }

    public static function queueListName(): array
    {
        return [
            'adrecord_parse_row' => self::ADRECORD,
            'andraction_parse_row' => self::ADTRACTION,
            'awin_parse_row' => self::AWIN,
            'trade_doubler_parse_row' => self::TRADE_DOUBLER,
        ];
    }
    
    public static function getRealShopNameByKey(string $name)
    {
        if (isset(self::getShopNamesMapping()[$name])) {
            return self::getShopNamesMapping()[$name];
        } else {
            throw new \Exception('shop ' . $name . ' not present on resources');   
        }
    }

    public static function getMapShopKeyByOriginalName(string $name)
    {
        return array_search($name, self::getShopNamesMapping());
    }

    public function getDataFroSlug()
    {
        return $this->shopName;
    }

    public function getNameForSeoDefaultTemplate()
    {
        return $this->shopName;
    }

    public function checkFileExist($name)
    {
        $isCheck = false;
        $files = $this->getFiles()->getValues();
        foreach ($files as $file) {
            /** @var Files $file */
            $isCheck = ($file->getOriginalName() === $name);
            if ($isCheck) {
                break;
            }
        }

        return $isCheck;
    }

    /**
     * @return Collection|Files[]
     */
    public function getFiles(): Collection
    {
        if (!$this->files) {
            $this->files = new ArrayCollection();
        }
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setShop($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getShop() === $this) {
                $file->setShop(null);
            }
        }

        return $this;
    }
}
