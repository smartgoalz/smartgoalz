<?php namespace Smartgoalz\Repositories\Note;

interface NoteRepository {

        public function getAll();

        public function get($id);

        public function create($data);

        public function update($id, $data);

        public function destroy($id);

}
