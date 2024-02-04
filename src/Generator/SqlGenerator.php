<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/* @noinspection PhpSignatureMismatchDuringInheritanceInspection */


declare(strict_types=1);


namespace Easy\Generator;

use App\Setting\Model\SettingGenerateTables;
use App\System\Model\SystemMenu;
use Hyperf\DbConnection\Db;
use Hyperf\Support\Filesystem\Filesystem;
use Easy\Exception\NormalStatusException;
use Easy\Helper\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 菜单SQL文件生成
 * Class SqlGenerator.
 */
class SqlGenerator extends EasyGenerator implements CodeGenerator
{
    protected SettingGenerateTables $model;

    protected string $codeContent;

    protected Filesystem $filesystem;

    protected int $adminId;

    /**
     * 设置生成信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function setGenInfo(SettingGenerateTables $model, int $adminId): SqlGenerator
    {
        $this->model = $model;
        $this->adminId = $adminId;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     * @throws \Exception
     */
    public function generator(): void
    {
        $path = BASE_PATH . "/runtime/generate/{$this->getShortBusinessName()}Menu.sql";
        $this->filesystem->makeDirectory(BASE_PATH . '/runtime/generate/', 0755, true, true);
        $this->filesystem->put($path, $this->placeholderReplace()->getCodeContent());

        if ($this->model->build_menu === self::YES) {
            Db::connection('default')->getPdo()->exec(
                str_replace(["\r", "\n"], ['', ''], $this->replace()->getCodeContent())
            );
        }
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
        return $this->getStubDir() . '/Sql/main.stub';
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
     * @throws \Exception
     */
    protected function placeholderReplace(): SqlGenerator
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
            '{LOAD_MENU}',
            '{PARENT_ID}',
            '{TABLE_NAME}',
            '{LEVEL}',
            '{NAME}',
            '{CODE}',
            '{ROUTE}',
            '{VUE_TEMPLATE}',
            '{ADMIN_ID}',
        ];
    }

    /**
     * 获取要替换占位符的内容.
     * @throws \Exception
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->getLoadMenu(),
            $this->getParentId(),
            $this->getTableName(),
            $this->getLevel(),
            $this->model->menu_name,
            $this->getCode(),
            $this->getRoute(),
            $this->getVueTemplate(),
            $this->getAdminId(),
        ];
    }

    protected function getLoadMenu(): string
    {
        $menus = $this->model->generate_menus ? explode(',', $this->model->generate_menus) : [];
        $ignoreMenus = ['realDelete', 'recovery', 'changeStatus', 'numberOperation'];

        foreach ($ignoreMenus as $menu) {
            if (in_array($menu, $menus)) {
                unset($menus[array_search($menu, $menus)]);
            }
        }

        $sql = '';
        $path = $this->getStubDir() . '/Sql/';
        foreach ($menus as $menu) {
            $content = $this->filesystem->sharedGet($path . $menu . '.stub');
            $sql .= $content;
        }
        return $sql;
    }

    /**
     * 获取菜单父ID.
     */
    protected function getParentId(): int
    {
        return $this->model->belong_menu_id;
    }

    /**
     * 获取菜单表表名.
     */
    protected function getTableName(): string
    {
        return env('DB_PREFIX') . SystemMenu::getModel()->getTable();
    }

    /**
     * 获取菜单层级value.
     */
    protected function getLevel(): string
    {
        if ($this->model->belong_menu_id !== 0) {
            $model = SystemMenu::find($this->model->belong_menu_id, ['id', 'level']);
            return $model->level . ',' . $model->id;
        }
        return '0';
    }

    /**
     * 获取菜单标识代码
     */
    protected function getCode(): string
    {
        return Str::lower($this->model->module_name) . ':' . $this->getShortBusinessName();
    }

    /**
     * 获取vue router地址
     */
    protected function getRoute(): string
    {
        return Str::lower($this->model->module_name) . '/' . $this->getShortBusinessName();
    }

    /**
     * 获取Vue模板路径.
     */
    protected function getVueTemplate(): string
    {
        return Str::lower($this->model->module_name) . '/' . $this->getShortBusinessName() . '/index';
    }

    /**
     * 获取当前登陆人ID.
     */
    protected function getAdminId(): string
    {
        return (string) $this->adminId;
    }
}
