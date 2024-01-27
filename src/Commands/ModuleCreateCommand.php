<?php

namespace Kindness\ModuleManage\Commands;

use Kindness\ModuleManage\Generators\FolderGenerator;
use Kindness\ModuleManage\Generators\StubGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kindness\ModuleManage\Utils\UtilStr;
use Webman\Config;

class ModuleCreateCommand extends Command
{
    protected static $defaultName = 'module:create';
    protected static $defaultDescription = '新建一个模块';

    /**
     * 执行命令
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->input = $input;

        $name = $this->setModuleName($input, $output);
        $name = trim($name);
        $studlyName = UtilStr::getStudlyName($name);

        $author = $this->setAuthorName($input, $output);

        $email = $this->ask('请输入邮箱地址（选填）');
        $description = $this->ask('请输入应用简介（选填）');
        $homepage = $this->ask('请输入主页地址（选填）');

        // Define the market namespace
        $namespace = Config::get('plugin.kindness.module-manage.app.namespace', 'plugin') . '\\' . $name;
        $namespaceComposer = Config::get('plugin.kindness.module-manage.app.namespace', 'plugin') . '\\\\' . $name . '\\\\';

        $replaces = [
            'name' => $name,
            'studlyName' => $studlyName,
            'lowerName' => UtilStr::getLowerName($name),
            'upperName' => UtilStr::getUpperName($name),
            'access_secret_key' => bin2hex(random_bytes(32)),
            'refresh_secret_key' => bin2hex(random_bytes(32)),
            'passphrase' => substr(bin2hex(random_bytes(32)), 0, 32),
            'iv' => substr(bin2hex(random_bytes(32)), 0, 16),
            'author' => $author,
            'email' => $email ?: "{$author}@{$author}.com",
            'description' => $description,
            'homepage' => $homepage ?: "https://{$author}.com",
            'namespace' => $namespace,
            'namespaceComposer' => $namespaceComposer
        ];

        // Generator folders
        with(new FolderGenerator($name, $this))->generator();

        // Generator stubs
        with(new StubGenerator($name, $this))
            ->setReplaces($replaces)
            ->generator();

        $this->info("应用 [$name] 已创建成功.");

        return self::SUCCESS;
    }

    public function setModuleName(InputInterface $input, OutputInterface $output)
    {
        $name = $this->ask('请输入模块名（必填）');
        if (empty($name)) {
            return $this->setModuleName($input, $output);
        }
        if (is_dir($plugin_config_path = base_path() . "/plugin/$name")) {
            $this->error("module:$name already exists");
            return $this->setModuleName($input, $output);
        }
        return $name;
    }

    public function setAuthorName(InputInterface $input, OutputInterface $output)
    {
        $name = $this->ask('请输入作者姓名（必填）');
        if (empty($name)) {
            return $this->setAuthorName($input, $output);
        }
        return $name;
    }
}
