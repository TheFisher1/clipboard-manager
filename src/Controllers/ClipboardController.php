<?php

require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Helpers/Response.php';

class ClipboardController
{
    private ClipboardRepository $repo;

    public function __construct()
    {
        $this->repo = new ClipboardRepository();
    }

    public function getAllClipboards()
    {
        $clipboards = $this->repo->findAll();
        Response::json($clipboards);
    }

    public function createClipboard()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['name'])) {
            return Response::json(['error' => 'Invalid data'], 400);
        }

        $clipboard = $this->repo->create($input);
        Response::json($clipboard, 201);
    }

    public function getClipboardById($id)
    {
        $clipboard = $this->repo->findById($id);

        if (!$clipboard) {
            return Response::json(['error' => 'Clipboard not found'], 404);
        }

        Response::json($clipboard);
    }

    public function updateClipboard($id)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $clipboard = $this->repo->findById($id);

    if (!$clipboard) {
        return Response::json(['error' => 'Clipboard not found'], 404);
    }

    if (isset($input['name'])) {
        $clipboard->setName($input['name']);
    }

    if (array_key_exists('description', $input)) {
        $clipboard->setDescription($input['description']);
    }

    if (array_key_exists('group_id', $input)) {
        $clipboard->setGroupId($input['group_id']);
    }

    if (array_key_exists('is_public', $input)) {
        $clipboard->setPublic((bool)$input['is_public']);
    }

    if (array_key_exists('max_subscribers', $input)) {
        $clipboard->setMaxSubscribers($input['max_subscribers']);
    }

    if (array_key_exists('max_items', $input)) {
        $clipboard->setMaxItems($input['max_items']);
    }

    if (array_key_exists('allowed_content_types', $input)) {
        $clipboard->setAllowedContentTypes($input['allowed_content_types']);
    }

    if (array_key_exists('default_expiration_minutes', $input)) {
        $clipboard->setDefaultExpirationMinutes($input['default_expiration_minutes']);
    }

    $updated = $this->repo->update($clipboard);

    Response::json($updated);
}


    public function deleteClipboard($id)
    {
        $clipboard = $this->repo->findById($id);

        if (!$clipboard) {
            return Response::json(['error' => 'Clipboard not found'], 404);
        }

        $this->repo->delete($id);
        Response::json(null, 204);
    }
}
