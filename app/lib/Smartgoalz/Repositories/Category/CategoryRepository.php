<?php namespace Smartgoalz\Repositories\Category;

interface CategoryRepository {

        public function getAll();

        public function get($id);

        public function create($data);

        public function update($id, $data);

        public function destroy($id);

}
