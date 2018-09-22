<?php

namespace Dotenv;

use Dotenv\Exception\InvalidPathException;

/**
 * This is the dotenv class.
 *
 * It's responsible for loading a `.env` file in the given directory and
 * setting the environment vars.
 */
class Dotenv
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The loader instance.
     * dotenv加载器实例
     * @var \Dotenv\Loader|null
     */
    protected $loader;

    /**
     * Create a new dotenv instance.
     * 创建一个新的dotenv实例
     * @param string $path
     * @param string $file
     *
     * @return void
     */
    public function __construct($path, $file = '.env')
    {
        // filePath 就是环境配置文件的文件路径信息
        $this->filePath = $this->getFilePath($path, $file);  //默认的 $path 就是basePath 也就是.env所在目录  $file 为.env
        $this->loader = new Loader($this->filePath, true);  //loader就是环境配置文件加载器、解析器
    }

    /**
     * Load environment file in given directory.
     * 加载指定目录中的环境配置文件
     * @return array
     */
    public function load()
    {
        return $this->loadData();
    }

    /**
     * Load environment file in given directory, suppress InvalidPathException.
     *
     * @return array
     */
    public function safeLoad()
    {
        try {
            return $this->loadData();
        } catch (InvalidPathException $e) {
            // suppressing exception
            return array();
        }
    }

    /**
     * Load environment file in given directory.
     *
     * @return array
     */
    public function overload()
    {
        return $this->loadData(true);
    }

    /**
     * Returns the full path to the file.
     * 获取环境配置文件的完整路径
     * @param string $path
     * @param string $file
     *
     * @return string
     */
    protected function getFilePath($path, $file)
    {
        if (!is_string($file)) {
            $file = '.env';
        }

        $filePath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;

        return $filePath;
    }

    /**
     * Actually load the data.
     * 真正的加载配置数据方法
     * @param bool $overload
     *
     * @return array
     */
    protected function loadData($overload = false)
    {
        // setImmutable设置环境配置文件中的变量是否能被覆盖
        return $this->loader->setImmutable(!$overload)->load();
    }

    /**
     * Required ensures that the specified variables exist, and returns a new validator object.
     * 需要确保指定的变量存在，并返回一个新的验证器对象。
     * @param string|string[] $variable
     *
     * @return \Dotenv\Validator
     */
    public function required($variable)
    {
        return new Validator((array) $variable, $this->loader);
    }

    /**
     * Get the list of environment variables declared inside the 'env' file.
     * 获取在env文件声明的环境变量列表
     * @return array
     */
    public function getEnvironmentVariableNames()
    {
        return $this->loader->variableNames;
    }
}
