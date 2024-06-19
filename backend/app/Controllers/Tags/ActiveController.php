<?php

namespace AddressBook\Controllers\Tags;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\TagRepository;
use AddressBook\Responses\JsonResponse;

class ActiveController implements ControllerInterface
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index(...$urlParams): void
    {
        $tags = $this->tagRepository->getActive();
        JsonResponse::handle($tags);
    }
}