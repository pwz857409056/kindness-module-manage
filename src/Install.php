<?php

namespace Kindness\ModuleManage;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = [
        'config/plugin/kindness/module-manage' => 'config/plugin/kindness/module-manage'
    ];

    /**
     * @var array
     */
    protected static $overwriteRelationPath = [
        'config/plugin/kindness/module-manage/app.php' => 'config/plugin/kindness/module-manage/app.php', // for v 1.0.3
    ];

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        static::installByRelation();
        static::overwriteByRelationPath();
        static::installEnv();
    }

    public static function installEnv()
    {
        if (!file_exists(base_path() . '/.env')) {
            \copy(__DIR__ . "/.env", base_path().'/.env');
        }
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            //symlink(__DIR__ . "/$source", base_path()."/$dest");
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest");
            echo "Create $dest
";
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest
";
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
    }

    /**
     * @return void
     */
    public static function overwriteByRelationPath()
    {
        foreach (static::$overwriteRelationPath as $source => $dest) {
            \copy(__DIR__ . "/$source", base_path() . "/$dest");
            echo "Update $dest
";
        }
    }

}
