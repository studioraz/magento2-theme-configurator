<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;

abstract class AbstractFilesystem
{
    const LESS_DIRECTORY_PREFIX_PATH = '/app/design';
    const LESS_DIRECTORY_SUFFIX_PATH = '/web/css/source';

    /**
     * Content Markers
     *
     * NOTE: they are used to detect custom SR/ThemeConfigurator code-block in the Less content string
     */
    const LESS_CONTENT_MARKER = '//>>>sr:theme-configurator';
    const LESS_CONTENT_MARKER_BEGIN = self::LESS_CONTENT_MARKER . ':start';
    const LESS_CONTENT_MARKER_END = self::LESS_CONTENT_MARKER . ':end';

    protected $injectionRecipientFile = '_theme.less';
    protected $fileBasename = '_theme_srthemeconfig.less';
    protected $tmpDirPath = '/var/srthemeconfig/tmp';
    protected $archiveDirPath = '/var/srthemeconfig/archive';

    /**
     * @var ReadInterface
     */
    protected $readAdapter;

    /**
     * @var WriteInterface
     */
    protected $writeAdapter;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * AbstractFilesystem constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function setConfig(array $config = [])
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns Write Adapter
     *
     * @param string $directoryCode
     * @return WriteInterface
     * @throws FileSystemException
     */
    protected function getWriteAdapter($directoryCode = DirectoryList::ROOT)
    {
        if (is_null($this->writeAdapter)) {
            $this->writeAdapter = $this->filesystem->getDirectoryWrite($directoryCode);
        }

        return $this->writeAdapter;
    }

    /**
     * Returns Read Adapter
     *
     * @param string $directoryCode
     * @return ReadInterface
     */
    protected function getReadAdapter($directoryCode = DirectoryList::ROOT)
    {
        if (is_null($this->readAdapter)) {
            $this->readAdapter = $this->filesystem->getDirectoryRead($directoryCode);
        }

        return $this->readAdapter;
    }

    /**
     * Returns content's Header
     *
     * @return string
     */
    protected function getContentHeader()
    {
        return <<<EOT
// /**
//  * Copyright © 2018 Studio Raz. All rights reserved.
//  * See LICENSE.txt for license details.
//  */

//
//  Overrides theme by custom values of Less Variables
//  __________________________________________________

//  Theme file should contain declarations (overrides) ONLY OF EXISTING variables
//  Otherwise this theme won't be available for parent nesting
//  All new variables should be placed in local theme lib or local theme files

EOT;
    }

    /**
     * Returns Relative path to directory of LessFile path
     *
     * @param string $area
     * @param string $themeCode
     * @return string
     */
    protected function buildBaseLessSourceDirPath($area, $themeCode)
    {
        return self::LESS_DIRECTORY_PREFIX_PATH . '/' . $area . '/' . $themeCode . self::LESS_DIRECTORY_SUFFIX_PATH;
    }

    /**
     * Returns Absolute Path of the destination directory
     *
     * @return string
     * @throws FileSystemException
     */
    protected function buildDestinationDirPath()
    {
        $config = $this->getConfig();
        $path = $this->buildBaseLessSourceDirPath($config['area'], $config['theme_code']);

        $directoryWriter = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $directoryWriter->create($path);

        return $directoryWriter->getAbsolutePath($path);
    }
}
