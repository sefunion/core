<?php


declare(strict_types=1);


namespace Easy\Generator;

use App\Setting\Model\SettingGenerateColumns;
use App\Setting\Model\SettingGenerateTables;
use Hyperf\Database\Model\Collection;
use Hyperf\Support\Filesystem\Filesystem;
use Easy\Exception\NormalStatusException;
use Easy\Helper\Str;

class DtoGenerator extends EasyGenerator implements CodeGenerator
{
    protected SettingGenerateTables $model;

    protected string $codeContent;

    protected Filesystem $filesystem;

    protected Collection $columns;

    /**
     * 设置生成信息.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): DtoGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);

        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get(['column_name', 'column_comment']);

        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::title($this->model->module_name[0]) . mb_substr($this->model->module_name, 1);
        if ($this->model->generate_type === 1) {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Dto/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Dto/";
        }
        $this->filesystem->exists($path) || $this->filesystem->makeDirectory($path, 0755, true, true);
        $this->filesystem->put($path . "{$this->getClassName()}.php", $this->replace()->getCodeContent());
    }

    public function preview(): string
    {
        return $this->replace()->getCodeContent();
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
        return $this->getStubDir() . '/dto.stub';
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
    protected function placeholderReplace(): DtoGenerator
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
            '{COMMENT}',
            '{CLASS_NAME}',
            '{LIST}',
        ];
    }

    /**
     * 获取要替换占位符的内容.
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getComment(),
            $this->getClassName(),
            $this->getList(),
        ];
    }

    /**
     * 初始化命名空间.
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . '\\Dto';
    }

    /**
     * 获取控制器注释.
     */
    protected function getComment(): string
    {
        return $this->model->menu_name . 'Dto （导入导出）';
    }

    /**
     * 获取类名称.
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName() . 'Dto';
    }

    protected function getList(): string
    {
        $phpCode = '';
        foreach ($this->columns as $index => $column) {
            $phpCode .= str_replace(
                ['NAME', 'INDEX', 'FIELD'],
                [$column['column_comment'] ?: $column['column_name'], $index, $column['column_name']],
                $this->getCodeTemplate()
            );
        }
        return $phpCode;
    }

    protected function getCodeTemplate(): string
    {
        return sprintf(
            "    %s\n    %s\n\n",
            '#[ExcelProperty(value: "NAME", index: INDEX)]',
            'public string $FIELD;'
        );
    }
}
