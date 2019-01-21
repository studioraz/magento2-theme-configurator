<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\FileContentInjector;

use SR\ThemeConfigurator\Model\Theme\AbstractFilesystem;

class LessFileContentInjector extends AbstractFilesystem implements FileContentInjectorInterface
{
    /**
     * @inheritdoc
     */
    public function inject($content = '', $destFile = null)
    {
        if (is_null($destFile)) {
            $destFile = $this->buildDestinationDirPath() . '/' . $this->injectionRecipientFile;
        }

        $readAdapter = $this->getReadAdapter();

        $destFileContent = $readAdapter->isExist($destFile) ? $readAdapter->readFile($destFile) : '';

        if (empty($destFileContent)) {
            $destFileContent = $this->getContentHeader();
            $destFileContent .= "\n\n\n" . self::LESS_CONTENT_MARKER_BEGIN . "\n" . $content . "\n" . self::LESS_CONTENT_MARKER_END;
            $isContentUpdated = true;
        } else if (strpos($destFileContent, self::LESS_CONTENT_MARKER) === false) {
            $destFileContent .= "\n\n\n" . self::LESS_CONTENT_MARKER_BEGIN . "\n" . $content . "\n" . self::LESS_CONTENT_MARKER_END;
            $isContentUpdated = true;
        } else {
            // @todo: implement logic to replace custom content
            $isContentUpdated = false;
        }

        if ($isContentUpdated) {
            $writeAdapter = $this->getWriteAdapter();
            $writeAdapter->delete($destFile);
            $writeAdapter->writeFile($destFile, $destFileContent);
        }

        return true;
    }
}
