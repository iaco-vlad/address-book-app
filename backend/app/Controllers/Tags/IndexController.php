<?php

namespace AddressBook\Controllers\Tags;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\TagRepository;
use AddressBook\Responses\JsonResponse;

class IndexController implements ControllerInterface
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index(...$urlParams): void
    {
        $tags = $this->tagRepository->all();
        JsonResponse::handle($tags);
    }
}