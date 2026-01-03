<?php
namespace BigConnect\CustomOptionPlus\Plugin\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Translation
{
    protected $localeResolver;
    protected $translations = [];
    protected $directoryList;
    protected $defaultStoreTitle;

    public function __construct(
        \Magento\Framework\Locale\Resolver $localeResolver,
        Filesystem $filesystem
    ) {
        $this->localeResolver = $localeResolver;
        $this->directoryList = $filesystem->getDirectoryRead(DirectoryList::APP);
        $this->loadTranslations();
    }

    protected function loadTranslations()
    {
        $localeCode = $this->localeResolver->getLocale();

        // Načítajte CSV súbor pre aktuálny jazyk, ak je k dispozícii
        $csvFilePath = $this->directoryList->getAbsolutePath() . "code/BigConnect/CustomOptionPlus/i18n/{$localeCode}.csv";
        if (file_exists($csvFilePath)) {
            $translations = array_map('str_getcsv', file($csvFilePath));
            foreach ($translations as $translation) {
                if (count($translation) === 2) {
                    $this->translations[$translation[0]] = $translation[1];
                    $this->defaultStoreTitle = $translation[0]; // pridajte toto
                }
            }
        }
    }

    public function afterGetTitle($subject, $result)
    {
        if ($subject instanceof \Magento\Catalog\Model\Product\Option\Value) {
            // Ak je aktuálne nastavené ID obchodu 0 (tj. predvolený obchod), vráťte pôvodný názov bez prekladu
            if ($subject->getStoreId() == 1) {
                return $result;
            }

            // Ak máme preklad pre aktuálny názov, vrátime preložený názov
            if (isset($this->translations[$result])) {
                return $this->translations[$result];
            }

            // Ak preklad neexistuje, vráťte pôvodný názov
            return $result;
        } elseif ($subject instanceof \Magento\Catalog\Model\Product\Option) {
            // Ak je aktuálne nastavené ID obchodu 0 (tj. predvolený obchod), vráťte pôvodný názov bez prekladu
            if ($subject->getStoreId() == 1) {
                return $result;
            }

            // Ak máme preklad pre aktuálny názov, vrátime preložený názov
            if (isset($this->translations[$result])) {
                return $this->translations[$result];
            }

            // Ak preklad neexistuje, vráťte pôvodný názov
            return $result;
        }

        return $result;
    }


}
