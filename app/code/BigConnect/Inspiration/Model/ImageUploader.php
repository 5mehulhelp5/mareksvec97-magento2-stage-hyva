<?php
namespace BigConnect\Inspiration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class ImageUploader
{
    private string $baseTmpPath;
    private string $basePath;
    private array $allowedExtensions;
    private UploaderFactory $uploaderFactory;
    private WriteInterface $mediaDirectory;
    private StoreManagerInterface $storeManager;

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        string $baseTmpPath,
        string $basePath,
        array $allowedExtensions
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->baseTmpPath = $baseTmpPath;
        $this->basePath = $basePath;
        $this->allowedExtensions = $allowedExtensions;
    }

    public function saveFileToTmpDir(string $fileId): array
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($this->baseTmpPath));
        if (!$result) {
            throw new LocalizedException(__('File cannot be saved to the destination folder.'));
        }

        $result['url'] = $this->getMediaUrl($this->baseTmpPath . '/' . $result['file']);
        $result['name'] = $result['file'];
        $result['file'] = $result['file'];

        return $result;
    }

    public function moveFileFromTmp(string $fileName): string
    {
        $tmpPath = $this->baseTmpPath . '/' . ltrim($fileName, '/');
        $destination = $this->basePath . '/' . ltrim($fileName, '/');

        if ($this->mediaDirectory->isExist($destination)) {
            return $destination;
        }

        $this->mediaDirectory->renameFile($tmpPath, $destination);

        return $destination;
    }

    public function getMediaUrl(string $path): string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . ltrim($path, '/');
    }
}
