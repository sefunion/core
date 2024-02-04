<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\Support\Filesystem\Filesystem;

class Easy
{
    private static string $easyName = 'EasyCMF';

    private static string $version = '1.5.1';

    private string $appPath = '';

    private array $moduleInfo = [];

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->setAppPath(BASE_PATH . '/app');
        $this->scanModule();
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function scanModule(): void
    {
        $modules = glob(self::getAppPath() . '*');
        $fs = container()->get(Filesystem::class);
        $infos = [];
        foreach ($modules as &$mod) {
            if (is_dir($mod)) {
                $modInfo = $mod . DIRECTORY_SEPARATOR . 'config.json';
                if (file_exists($modInfo)) {
                    $infos[basename($mod)] = json_decode($fs->sharedGet($modInfo), true);
                }
            }
        }
        $sortId = array_column($infos, 'order');
        array_multisort($sortId, SORT_ASC, $infos);
        $this->setModuleInfo($infos);
    }

    public static function getVersion(): string
    {
        return self::$version;
    }

    public static function getEasyName(): string
    {
        return self::$easyName;
    }

    /**
     * @return mixed
     */
    public function getAppPath(): string
    {
        return $this->appPath . DIRECTORY_SEPARATOR;
    }

    /**
     * @param mixed $appPath
     */
    public function setAppPath(string $appPath): void
    {
        $this->appPath = $appPath;
    }

    /**
     * @return mixed
     */
    public function getModuleInfo(string $name = null): array
    {
        if (empty($name)) {
            return $this->moduleInfo;
        }
        return $this->moduleInfo[$name] ?? [];
    }

    /**
     * @param mixed $moduleInfo
     */
    public function setModuleInfo($moduleInfo): void
    {
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * @param false $save
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setModuleConfigValue(string $key, string $value, bool $save = false): bool
    {
        if (strpos($key, '.') > 0) {
            [$mod, $name] = explode('.', $key);
            if (isset($this->moduleInfo[$mod], $this->moduleInfo[$mod][$name])) {
                $this->moduleInfo[$mod][$name] = $value;
                $save && $this->saveModuleConfig($mod);
                return true;
            }
        }
        return false;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function saveModuleConfig(string $mod): void
    {
        if (! empty($mod)) {
            $fs = container()->get(Filesystem::class);
            $modJson = $this->getAppPath() . $mod . DIRECTORY_SEPARATOR . 'config.json';
            if (! $fs->isWritable($modJson)) {
                $fs->chmod($modJson, 666);
            }
            $fs->put($modJson, \json_encode($this->getModuleInfo($mod), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
