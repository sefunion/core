<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\Command\Command as HyperfCommand;

/**
 * class EasyCommand.
 */
abstract class EasyCommand extends HyperfCommand
{
    protected const CONSOLE_GREEN_BEGIN = "\033[32;5;1m";

    protected const CONSOLE_RED_BEGIN = "\033[31;5;1m";

    protected const CONSOLE_END = "\033[0m";

    protected string $module;

    protected function getGreenText($text): string
    {
        return self::CONSOLE_GREEN_BEGIN . $text . self::CONSOLE_END;
    }

    protected function getRedText($text): string
    {
        return self::CONSOLE_RED_BEGIN . $text . self::CONSOLE_END;
    }

    protected function getStub($filename): string
    {
        return BASE_PATH . '/vendor/sefunion/core/src/Command/Creater/Stubs/' . $filename . '.stub';
    }

    protected function getModulePath(): string
    {
        return BASE_PATH . '/app/' . $this->module . '/Request/';
    }

    protected function getInfo(): string
    {
        return sprintf('EasyCMF', date('Y'));
    }

    public function getParams()
    {
        $options = $this->input->getOptions();
        if (empty($this->getRules())) {
            return $options;
        }

        // Validator::make($options, $this->getRules());
        return $options;
    }

    protected function getRules()
    {
        return [];
    }

}
