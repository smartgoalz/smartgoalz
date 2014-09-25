<?php namespace Smartgoalz\Repositories\Note;

use Note;
use Smartgoalz\Repositories\BaseRepository;

class EloquentNoteRepository extends BaseRepository implements NoteRepository {

        protected $note;

        public function __construct(Note $note)
        {
                parent::__construct($note);
                $this->note = $note;
        }

        public function getAll()
        {
                return $this->note->curUser()->orderBy('created_at', 'DESC')->get();
        }

        public function get($id)
        {
                return $this->note->curUser()->find($id);
        }

        public function create($data)
        {
                return $this->note->create($data);
        }

        public function update($id, $data)
        {
                $note = $this->note->curUser()->find($id);
                if (!$note) {
                        return false;
                }

                $note->title = $data['title'];
                $note->pin_dashboard = $data['pin_dashboard'];
                $note->pin_top = $data['pin_top'];
                $note->note = $data['note'];

                return $note->save();
        }

        public function destroy($id)
        {
                $note = $this->note->curUser()->find($id);
                if (!$note) {
                        return false;
                }
                return $note->delete();
        }
}
