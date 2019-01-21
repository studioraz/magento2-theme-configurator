<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\FileGenerator;

use Magento\Framework\Exception\FileSystemException;
use SR\ThemeConfigurator\Model\Theme\AbstractFilesystem;

class LessFileGenerator extends AbstractFilesystem implements FileGeneratorInterface
{
    /**
     * Full path of the generated file (Absolute or Relative)
     *
     * @var string
     */
    private $generatedFile;

    /**
     * @inheritdoc
     */
    public function generate($content = '')
    {
        $destDirPath = $this->buildDestinationDirPath();

        $destFileName = $this->fileBasename;
        $destFilePath = $destDirPath . '/' . $destFileName;

        $this->prepareContent($content);

        $directoryWriter = $this->getWriteAdapter();
        $directoryWriter->delete($destFilePath);// remove existing file
        $directoryWriter->writeFile($destFilePath, $content);

        $this->setGeneratedFile($destFilePath);

        $this->createArchiveCopy();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function setGeneratedFile($generatedFile = null)
    {
        $this->generatedFile = $generatedFile;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGeneratedFile()
    {
        return $this->generatedFile;
    }

    /**
     * Prepares content
     * Note: modifies passed variable
     *
     * @param string $content
     * @return $this
     */
    private function prepareContent(&$content = '')
    {
        $content = $this->getContentHeader() . "\n\n\n". self::LESS_CONTENT_MARKER_BEGIN . "\n" . (string)$content . "\n" . self::LESS_CONTENT_MARKER_END;
        $content .= "\n";//add blank line and the end
        return $this;
    }

    /**
     * @return bool
     * @throws FileSystemException
     */
    private function createArchiveCopy()
    {
        $directoryWriter = $this->getWriteAdapter();

        $archiveDirPath = $this->archiveDirPath;
        $directoryWriter->create($archiveDirPath);

        $archiveFileName = 'theme_variables_' . date('Ymd') . '_' . date('His') . '_' . $this->fileBasename;
        $archiveFilePath = $directoryWriter->getAbsolutePath($archiveDirPath) . '/' . $archiveFileName;

        $directoryWriter->copyFile($this->getGeneratedFile(), $archiveFilePath);

        return true;
    }
}
