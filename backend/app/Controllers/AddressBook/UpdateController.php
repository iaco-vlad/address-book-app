<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Models\ContactTag;
use AddressBook\Models\Tag;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Repositories\ContactTagRepository;
use AddressBook\Repositories\TagRepository;
use AddressBook\Responses\JsonResponse;

class UpdateController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;
    private TagRepository $tagRepository;
    private ContactTagRepository $contactTagRepository;

    public function __construct(
        AddressBookRepository $addressBookRepository,
        TagRepository $tagRepository,
        ContactTagRepository $contactTagRepository
    )
    {
        $this->addressBookRepository = $addressBookRepository;
        $this->tagRepository = $tagRepository;
        $this->contactTagRepository = $contactTagRepository;
    }

    public function index(...$urlParams): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data === null) {
            JsonResponse::handle(['message' => 'Invalid JSON'], 400);
        }

        $requiredFields = ['last_name', 'first_name', 'email', 'street', 'zip_code'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                JsonResponse::handle(['message' => "Missing required field: $field"], 400);
            }
        }
        $id = $urlParams[0];

        $address = $this->addressBookRepository->find($id);

        if (!$address) {
            JsonResponse::handle(['message' => 'Address not found'], 404);
        }

        $address->last_name = $data['last_name'];
        $address->first_name = $data['first_name'];
        $address->email = $data['email'];
        $address->street = $data['street'];
        $address->zip_code = $data['zip_code'];
        $address->city_id = $data['city_id'];

        $this->addressBookRepository->update($id, $address);

        // Remove existing contact tags
        $this->contactTagRepository->deleteByContactId($id);

        if (isset($data['new_tags'])) {
            $newTags = explode(',', $data['new_tags']);
            foreach ($newTags as $tagName) {
                $tagName = trim($tagName);
                if (!empty($tagName)) {
                    $tag = new Tag();
                    $tag->name = $tagName;
                    $tag = $this->tagRepository->create($tag);

                    $contactTag = new ContactTag();
                    $contactTag->contact_id = $address->id;
                    $contactTag->tag_id = $tag->id;
                    $this->contactTagRepository->create($contactTag, true);
                }
            }
        }

        if (isset($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tagId) {
                $contactTag = new ContactTag();
                $contactTag->contact_id = $address->id;
                $contactTag->tag_id = $tagId;
                $this->contactTagRepository->create($contactTag, true);
            }
        }

        JsonResponse::handle($address->toArray());
    }
}