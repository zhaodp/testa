<?php

/**
 * Class AssetManager
 *
 * 重写AssetManger
 */
class AssetManager extends CAssetManager
{

    public $useHashByName = true;

    /**
     * 重写发布路径生成规则
     * @param string $file
     * @param bool $hashByName
     * @return string
     */
    protected function generatePath($file,$hashByName=false)
    {
        $hashByName = $this->useHashByName;
        if (is_file($file))
            $pathForHashing=$hashByName ? dirname($file) : dirname($file).filemtime($file);
        else
            $pathForHashing=$hashByName ? $file : $file.filemtime($file);

        return $this->hash($pathForHashing);
    }

}
