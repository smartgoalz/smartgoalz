<?php namespace Smartgoalz\Repositories\Category;

use Category;
use Smartgoalz\Repositories\BaseRepository;

class EloquentCategoryRepository extends BaseRepository implements CategoryRepository {

        protected $category;

        public function __construct(Category $category)
        {
                parent::__construct($category);
                $this->category = $category;
        }

        public function getAll()
        {
                return $this->category->curUser()->orderBy('title', 'DESC')->get();
        }

        public function get($id)
        {
                return $this->category->curUser()->find($id);
        }

        public function create($data)
        {
                return $this->category->create($data);
        }

        public function update($id, $data)
        {
                $category = $this->category->curUser()->find($id);
                if (!$category) {
                        return false;
                }

                $category->title = $data['title'];

                return $category->save();
        }

        public function destroy($id)
        {
                $category = $this->category->curUser()->find($id);
                if (!$category) {
                        return false;
                }
                return $category->delete();
        }
}
