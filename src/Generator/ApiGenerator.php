<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/* @noinspection PhpSignatureMismatchDuringInheritanceInspection */


declare(strict_types=1);

namespace Easy\Generator;

use App\Setting\Model\SettingGenerateTables;
use Hyperf\Support\Filesystem\Filesystem;
use Easy\Exception\NormalStatusException;
use Easy\Helper\Str;

/**
 * JS API文件生成
 * Class ApiGenerator.
 */
class ApiGenerator extends EasyGenerator implements CodeGenerator
{
    protected SettingGenerateTables $model;

    protected string $codeContent;

    protected Filesystem $filesystem;

    /**
     * 设置生成信息.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): ApiGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $filename = Str::camel(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
        $module = Str::lower($this->model->module_name);
        $this->filesystem->makeDirectory(BASE_PATH . "/runtime/generate/vue/src/api/{$module}", 0755, true, true);
        $path = BASE_PATH . "/runtime/generate/vue/src/api/{$module}/{$filename}.js";
        $this->filesystem->put($path, $this->replace()->getCodeContent());
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->replace()->getCodeContent();
    }

    /**
     * 获取短业务名称.
     */
    public function getShortBusinessName(): string
    {
        return Str::camel(str_replace(
            Str::lower($this->model->module_name),
            '',
            str_replace(env('DB_PREFIX'), '', $this->model->table_name)
        ));
    }

    /**
     * 设置代码内容.
     */
    public function setCodeContent(string $content)
    {
        $this->codeContent = $content;
    }

    /**
     * 获取代码内容.
     */
    public function getCodeContent(): string
    {
        return $this->codeContent;
    }

    /**
     * 获取模板地址
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir() . '/Api/main.stub';
    }

    /**
     * 读取模板内容.
     */
    protected function readTemplate(): string
    {
        return $this->filesystem->sharedGet($this->getTemplatePath());
    }

    /**
     * 占位符替换.
     */
    protected function placeholderReplace(): ApiGenerator
    {
        $this->setCodeContent(str_replace(
            $this->getPlaceHolderContent(),
            $this->getReplaceContent(),
            $this->readTemplate()
        ));

        return $this;
    }

    /**
     * 获取要替换的占位符.
     */
    protected function getPlaceHolderContent(): array
    {
        return [
            '{LOAD_API}',
            '{COMMENT}',
            '{BUSINESS_NAME}',
            '{REQUEST_ROUTE}',
        ];
    }

    /**
     * 获取要替换占位符的内容.
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->getLoadApi(),
            $this->getComment(),
            $this->getBusinessName(),
            $this->getRequestRoute(),
        ];
    }

    protected function getLoadApi(): string
    {
        $menus = $this->model->generate_menus ? explode(',', $this->model->generate_menus) : [];
        $ignoreMenus = ['realDelete', 'recovery'];

        array_unshift($menus, $this->model->type === 'single' ? 'singleList' : 'treeList');

        foreach ($ignoreMenus as $menu) {
            if (in_array($menu, $menus)) {
                unset($menus[array_search($menu, $menus)]);
            }
        }

        $jsCode = '';
        $path = $this->getStubDir() . '/Api/';
        foreach ($menus as $menu) {
            $content = $this->filesystem->sharedGet($path . $menu . '.stub');
            $jsCode .= $content;
        }

        return $jsCode;
    }

    /**
     * 获取控制器注释.
     */
    protected function getComment(): string
    {
        return $this->getBusinessName() . ' API JS';
    }

    /**
     * 获取请求路由.
     */
    protected function getRequestRoute(): string
    {
        return Str::lower($this->model->module_name) . '/' . $this->getShortBusinessName();
    }

    /**
     * 获取业务名称.
     */
    protected function getBusinessName(): string
    {
        return $this->model->menu_name;
    }
}
