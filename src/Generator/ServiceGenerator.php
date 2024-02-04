<?php

/** @noinspection PhpIllegalStringOffsetInspection */


declare(strict_types=1);


namespace Easy\Generator;

use App\Setting\Model\SettingGenerateTables;
use Hyperf\Support\Filesystem\Filesystem;
use Easy\Exception\NormalStatusException;
use Easy\Helper\Str;

/**
 * 服务类生成
 * Class ServiceGenerator.
 */
class ServiceGenerator extends EasyGenerator implements CodeGenerator
{
    protected SettingGenerateTables $model;

    protected string $codeContent;

    protected Filesystem $filesystem;

    /**
     * 设置生成信息.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): ServiceGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);
        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::title($this->model->module_name[0]) . mb_substr($this->model->module_name, 1);
        if ($this->model->generate_type === 1) {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Service/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Service/";
        }
        $this->filesystem->exists($path) || $this->filesystem->makeDirectory($path, 0755, true, true);
        $this->filesystem->put($path . "{$this->getClassName()}.php", $this->replace()->getCodeContent());
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->replace()->getCodeContent();
    }

    /**
     * 获取生成的类型.
     */
    public function getType(): string
    {
        return ucfirst($this->model->type);
    }

    /**
     * 获取业务名称.
     */
    public function getBusinessName(): string
    {
        return Str::studly(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
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
        return $this->getStubDir() . $this->getType() . '/service.stub';
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
    protected function placeholderReplace(): ServiceGenerator
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
            '{NAMESPACE}',
            '{USE}',
            '{COMMENT}',
            '{CLASS_NAME}',
            '{MAPPER}',
            '{FIELD_ID}',
            '{FIELD_PID}',
        ];
    }

    /**
     * 获取要替换占位符的内容.
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getUse(),
            $this->getComment(),
            $this->getClassName(),
            $this->getMapperName(),
            $this->getFieldIdName(),
            $this->getFieldPidName(),
        ];
    }

    /**
     * 初始化服务类命名空间.
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . '\\Service';
    }

    /**
     * 获取控制器注释.
     */
    protected function getComment(): string
    {
        return $this->model->menu_name . '服务类';
    }

    /**
     * 获取使用的类命名空间.
     */
    protected function getUse(): string
    {
        return <<<UseNamespace
use {$this->getNamespace()}\\Mapper\\{$this->getBusinessName()}Mapper;
UseNamespace;
    }

    /**
     * 获取控制器类名称.
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName() . 'Service';
    }

    /**
     * 获取Mapper类名称.
     */
    protected function getMapperName(): string
    {
        return $this->getBusinessName() . 'Mapper';
    }

    /**
     * 获取树表ID.
     */
    protected function getFieldIdName(): string
    {
        if ($this->getType() == 'Tree') {
            if ($this->model->options['tree_id'] ?? false) {
                return $this->model->options['tree_id'];
            }
            return 'id';
        }
        return '';
    }

    /**
     * 获取树表父ID.
     */
    protected function getFieldPidName(): string
    {
        if ($this->getType() == 'Tree') {
            if ($this->model->options['tree_pid'] ?? false) {
                return $this->model->options['tree_pid'];
            }
            return 'parent_id';
        }
        return '';
    }
}
