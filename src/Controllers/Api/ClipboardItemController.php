<?php

require_once __DIR__ . '/../../Core/Repository/ClipboardItemRepository.php';
require_once __DIR__ . '/../../Core/Repository/ClipboardRepository.php';
require_once __DIR__ . '/../../Core/Model/ClipboardItem.php';

class ClipboardItemController
{
    private ClipboardItemRepository $repository;
    private ClipboardRepository $clipboardRepository;
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;
    private const MAX_TEXT_LENGTH = 4096;

    public function __construct()
    {
        $this->repository = new ClipboardItemRepository();
        $this->clipboardRepository = new ClipboardRepository();
    }

    public function handleRequest(string $method, string $path, ?string $clipboardId, ?string $itemId, int $userId): void
    {
        try {
            switch ($method) {
                case 'GET':
                    if ($itemId && $path === 'view') {
                        $this->view((int)$itemId, $userId);
                        return;
                    }

                    if ($itemId && $path === 'download') {
                        $this->download((int)$itemId, $userId);
                        return;
                    }
    
                    $itemId
                        ? $this->getOne((int)$itemId, $userId)
                        : $this->getByClipboard((int)$clipboardId, $userId);
                    break;

                case 'POST':
                    if ($clipboardId && $path === 'file') {
                        $this->createFile((int)$clipboardId, $userId);
                        return;
                    }

                    $this->create((int)$clipboardId, $userId);
                    break;

                case 'PUT':
                    $this->update((int)$itemId, $userId);
                    break;

                case 'DELETE':
                    $this->delete((int)$itemId, $userId);
                    break;

                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function getByClipboard(int $clipboardId, int $userId): void
    {
        $clipboard = $this->clipboardRepository->findById($clipboardId);

        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot access items of clipboard thats not yours', 403);
            return;
        }

        $items = $this->repository->findByClipboardId($clipboardId);
        $this->sendResponse(array_map(fn($i) => $this->toArray($i), $items));
    }

    private function getOne(int $id, int $userId): void
    {
        $item = $this->repository->findById($id);
        if (!$item) {
            $this->sendError('Item not found', 404);
            return;
        }

        $clipboard = $this->clipboardRepository->findById($item->getClipboardId());
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot access items of clipboard thats not yours', 403);
            return;
        }

        $this->sendResponse($this->toArray($item));
    }

    private function create(int $clipboardId, int $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $clipboard = $this->clipboardRepository->findById($clipboardId);
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot add items to clipboard thats not yours', 403);
            return;
        }

        
        if (!isset($data['content_type'])) {
            $this->sendError('Missing required fields: content_type', 400);
            return;
        }

        if (isset($data['content_text']) && strlen($data['content_text']) > self::MAX_TEXT_LENGTH) {
            $this->sendError('content_text exceeds max length (4096)', 413);
        }

        $item = new ClipboardItem(
            $clipboardId,
            $data['content_type'],
            $userId,
            $data['content_text'] ?? null,
            $data['file_path'] ?? null,
            $data['original_filename'] ?? null,
            $data['file_size'] ?? null,
            $data['url'] ?? null,
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['expires_at'] ?? null,
            $data['is_single_use'] ?? false
        );

        $id = $this->repository->create($item);
        $created = $this->repository->findById($id);
        
        $this->sendResponse($this->toArray($created), 201);
    }

    private function createFile(int $clipboardId, int $userId): void
    {
        if (!isset($_FILES['file'])) {
            $this->sendError('Missing file', 400);
            return;
        }

        $clipboard = $this->clipboardRepository->findById($clipboardId);
        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }

        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('Forbidden', 403);
            return;
        }

        $uploadDir = __DIR__ . '/../../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalFilename = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];

        if ($fileSize > self::MAX_FILE_SIZE) {
            $this->sendError('File exceeds max size', 413);
            return;
        }

        $safeName = bin2hex(random_bytes(16));
        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $filename = $safeName . ($ext ? ".$ext" : '');

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $filename)) {
            $this->sendError('Upload failed', 500);
            return;
        }

        $item = new ClipboardItem(
            $clipboardId,
            $_POST['content_type'],
            $userId,
            null,
            'uploads/' . $filename,
            $originalFilename,
            $fileSize,
            null,
            $_POST['title'] ?? null,
            $_POST['description'] ?? null,
            null,
            false
        );

        $id = $this->repository->create($item);
        $created = $this->repository->findById($id);

        $this->sendResponse($this->toArray($created), 201);
    }

    private function view(int $itemId, int $userId): void
    {
        $item = $this->repository->findById($itemId);

        if (!$item || !$item->getFilePath()) {
            $this->sendError('File not found', 404);
            return;
        }

        $this->serveFile($item, $userId, 'inline');
    }

    private function download(int $itemId, int $userId): void
    {
        $item = $this->repository->findById($itemId);

        if (!$item || !$item->getFilePath()) {
            $this->sendError('File not found', 404);
            return;
        }

        $this->serveFile($item, $userId, 'attachment');
    }

    private function update(int $id, int $userId): void
    {
        $item = $this->repository->findById($id);
        if (!$item) {
            $this->sendError('Item not found', 404);
            return;
        }
        $clipboard = $this->clipboardRepository->findById($item->getClipboardId());
        
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot update items from clipboard thats not yours', 403);
            return;
        }

        if ($clipboard->isPublic() && $item->getSubmittedBy() !== $userId && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot update items that are not yours', 403);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['content_text']) && strlen($data['content_text']) > self::MAX_TEXT_LENGTH) {
            $this->sendError('content_text exceeds max length (4096)', 413);
        }
        
        if (isset($data['content_text'])) $item->setContentText($data['content_text']);
        if (isset($data['title'])) $item->setTitle($data['title']);
        if (isset($data['description'])) $item->setDescription($data['description']);
        if (isset($data['url'])) $item->setUrl($data['url']);

        $this->repository->update($item);
        $updated = $this->repository->findById($id);
        
        $this->sendResponse($this->toArray($updated));
    }

    private function delete(int $id, int $userId): void
    {
        $item = $this->repository->findById($id);
        if (!$item) {
            $this->sendError('Item not found', 404);
            return;
        }

        $clipboard = $this->clipboardRepository->findById($item->getClipboardId());
        
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot delete items from clipboard thats not yours', 403);
            return;
        }

        if ($clipboard->isPublic() && $item->getSubmittedBy() !== $userId && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('You cannot delete items that are not yours', 403);
            return;
        }

        $this->repository->delete($id);
        $this->sendResponse(['message' => 'Item deleted successfully']);
    }

    private function serveFile(
        ClipboardItem $item,
        int $userId,
        string $disposition // 'inline' or 'attachment'
    ): void {
        $clipboard = $this->clipboardRepository->findById($item->getClipboardId());
        if (!$clipboard) {
            $this->sendError('Clipboard not found', 404);
            return;
        }
    
        if (!$clipboard->isPublic() && $clipboard->getOwnerId() !== $userId) {
            $this->sendError('Forbidden', 403);
            return;
        }
    
        $filePath = __DIR__ . '/../../../' . $item->getFilePath();
        if (!file_exists($filePath)) {
            $this->sendError('File not found', 404);
            return;
        }
    
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        // finfo_close($finfo);
    
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header(
            'Content-Disposition: ' . $disposition .
            '; filename="' . $item->getOriginalFilename() . '"'
        );
    
        readfile($filePath);
        exit;
    }
    

    private function toArray(ClipboardItem $item): array
    {
        return [
            'id' => $item->getId(),
            'clipboard_id' => $item->getClipboardId(),
            'content_type' => $item->getContentType(),
            'content_text' => $item->getContentText(),
            'file_path' => $item->getFilePath(),
            'original_filename' => $item->getOriginalFilename(),
            'file_size' => $item->getFileSize(),
            'url' => $item->getUrl(),
            'title' => $item->getTitle(),
            'description' => $item->getDescription(),
            'submitted_by' => $item->getSubmittedBy(),
            'expires_at' => $item->getExpiresAt(),
            'view_count' => $item->getViewCount(),
            'download_count' => $item->getDownloadCount(),
            'is_single_use' => $item->isSingleUse(),
            'is_consumed' => $item->isConsumed(),
            'created_at' => $item->getCreatedAt()
        ];
    }

    private function sendResponse($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    private function sendError(string $message, int $code = 400): void
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
