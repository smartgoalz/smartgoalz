<?php namespace Smartgoalz\Repositories\Goal;

interface GoalRepository {

        public function getAll();

        public function get($id);

        public function create($data);

        public function update($id, $data);

        public function destroy($id);

}
