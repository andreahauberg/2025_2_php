<?php
require_once __DIR__ . '/../models/SearchModel.php';

class HashtagController {

    private SearchModel $model;

    public function __construct() {
        $this->model = new SearchModel();
    }

    public function handle($tag) {
        $cleanTag = trim($tag);

        return [
            "tag"   => $cleanTag,
            "posts" => $this->model->searchHashtag($cleanTag)
        ];
    }
}