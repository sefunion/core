<?php

declare(strict_types=1);

namespace Easy;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Easy\Exception\EasyException;
use Easy\Interfaces\EasyModelExcel;
use Easy\Interfaces\ServiceInterface\DictDataServiceInterface;
use Easy\EasyResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

abstract class EasyExcel
{
    public const ANNOTATION_NAME = 'Easy\Annotation\ExcelProperty';

    protected ?array $annotationMate;

    protected array $property = [];

    protected array $dictData = [];

    /**
     * 是否通过index进行排序
     * 否则使用属性在代码中的位置进行排序
     * 同时影响 导入和导出.
     */
    protected ?bool $orderByIndex;

    public function __construct(string $dto)
    {
        if (! (new $dto()) instanceof EasyModelExcel) {
            throw new EasyException('dto does not implement an interface of the EasyModelExcel', 500);
        }
        $dtoObject = new $dto();
        if (method_exists($dtoObject, 'dictData')) {
            $this->dictData = $dtoObject->dictData();
        }
        $this->annotationMate = AnnotationCollector::get($dto);
        $this->parseProperty();
    }

    public function getProperty(): array
    {
        return $this->property;
    }

    public function getAnnotationInfo(): array
    {
        return $this->annotationMate;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    protected function parseProperty(): void
    {
        if (empty($this->annotationMate) || ! isset($this->annotationMate['_c'])) {
            throw new EasyException('dto annotation info is empty', 500);
        }

        // 判断数组中任意一行包含 index键
        $this->orderByIndex = array_reduce(array_values($this->annotationMate['_p']), function ($carry, $item) {
            return $carry || isset($item[self::ANNOTATION_NAME]->index);
        }, false);

        foreach ($this->annotationMate['_p'] as $name => $mate) {
            $tmp = [
                'name' => $name,
                'value' => $mate[self::ANNOTATION_NAME]->value,
                'width' => $mate[self::ANNOTATION_NAME]->width ?? null,
                'align' => $mate[self::ANNOTATION_NAME]->align ?? null,
                'headColor' => $mate[self::ANNOTATION_NAME]->headColor ?? null,
                'headBgColor' => $mate[self::ANNOTATION_NAME]->headBgColor ?? null,
                'color' => $mate[self::ANNOTATION_NAME]->color ?? null,
                'bgColor' => $mate[self::ANNOTATION_NAME]->bgColor ?? null,
                'dictData' => $mate[self::ANNOTATION_NAME]->dictData,
                'dictName' => empty($mate[self::ANNOTATION_NAME]->dictName) ? null : $this->getDictData($mate[self::ANNOTATION_NAME]->dictName),
                'path' => $mate[self::ANNOTATION_NAME]->path ?? null,
            ];

            if ($this->orderByIndex) {
                $this->property[$mate[self::ANNOTATION_NAME]->index] = $tmp;
            } else {
                $this->property[] = $tmp;
            }
        }
        ksort($this->property);
    }

    /**
     * 下载excel.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function downloadExcel(string $filename, string $content): ResponseInterface
    {
        return container()->get(EasyResponse::class)->getResponse()
            ->withHeader('Server', 'EasyCMF')
            ->withHeader('access-control-expose-headers', 'content-disposition')
            ->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('content-disposition', "attachment; filename={$filename}; filename*=UTF-8''" . rawurlencode($filename))
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($content));
    }

    /**
     * 获取字典数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    protected function getDictData(string $dictName): array
    {
        $data = [];
        foreach (container()
            ->get(DictDataServiceInterface::class)
            ->getList(['code' => $dictName]) as $item) {
            $data[$item['key']] = $item['title'];
        }

        return $data;
    }

    /**
     * 获取 excel 列索引.
     */
    protected function getColumnIndex(int $columnIndex = 0): string
    {
        if ($columnIndex < 26) {
            return chr(65 + $columnIndex);
        }
        if ($columnIndex < 702) {
            return chr(64 + intval($columnIndex / 26)) . chr(65 + $columnIndex % 26);
        }
        return chr(64 + intval(($columnIndex - 26) / 676)) . chr(65 + intval((($columnIndex - 26) % 676) / 26)) . chr(65 + $columnIndex % 26);
    }
}