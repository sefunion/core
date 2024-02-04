<?php


declare(strict_types=1);


namespace Easy\Command;

use Hyperf\Command\Annotation\Command;
use Easy\Helper\Str;
use Easy\EasyCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class JwtCommand.
 */
#[Command]
class JwtCommand extends EasyCommand
{
    /**
     * 生成JWT密钥命令.
     */
    protected ?string $name = 'easy:jwt-gen';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php easy:gen-jwt" create the new jwt secret');
        $this->setDescription('EasyCMF system gen jwt command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $jwtSecret = Str::upper($this->input->getOption('jwtSecret'));

        if (empty($jwtSecret)) {
            $this->line('Missing parameter <--jwtSecret < jwt secret name>>', 'error');
        }

        $envPath = BASE_PATH . '/.env';

        if (! file_exists($envPath)) {
            $this->line('.env file not is exists!', 'error');
        }

        $key = base64_encode(random_bytes(64));

        if (Str::contains(file_get_contents($envPath), $jwtSecret) === false) {
            file_put_contents($envPath, "\n{$jwtSecret}={$key}\n", FILE_APPEND);
        } else {
            file_put_contents($envPath, preg_replace(
                "~{$jwtSecret}\\s*=\\s*[^\n]*~",
                "{$jwtSecret}=\"{$key}\"",
                file_get_contents($envPath)
            ));
        }

        $this->info('jwt secret generator successfully:' . $key);
    }

    protected function getOptions(): array
    {
        return [
            ['jwtSecret', '', InputOption::VALUE_REQUIRED, 'Please enter the jwtSecret to be generated'],
        ];
    }
}
