<?php

namespace Kindness\ModuleManage\Generators;

use Webman\Config;

class FolderGenerator extends Generator
{
    /**
     * Generator Folders.
     *
     * @return void
     */
    public function generator()
    {
        $generators = Config::get('plugin.kindness.module-manage.app.paths.generator', []);

        foreach ($generators as $key => $value) {
            $path = $this->path . DIRECTORY_SEPARATOR . $value;
            if (mkdir($path, 0755, true)) {
                $this->command->info("Created key: {$key} of generator config");
            } else {
                $this->command->error("Failed created key: {$key} of generator config");
            }
        }
    }
}
