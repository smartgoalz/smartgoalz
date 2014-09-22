<?php namespace Smartgoalz\Repositories;

abstract class BaseRepository {

        protected $model;

        public function __construct($model) {
                $this->model = $model;
        }

        public function create($data) {
                return $this->model->create($data);
        }

}
