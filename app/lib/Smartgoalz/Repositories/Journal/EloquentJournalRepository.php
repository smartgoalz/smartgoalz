<?php namespace Smartgoalz\Repositories\Journal;

use Journal;
use Smartgoalz\Repositories\BaseRepository;

class EloquentJournalRepository extends BaseRepository implements JournalRepository {

        protected $journal;

        public function __construct(Journal $journal)
        {
                parent::__construct($journal);
                $this->journal = $journal;
        }

        public function getAll()
        {
                return $this->journal->curUser()->orderBy('date', 'DESC')->get();
        }

        public function get($id)
        {
                return $this->journal->curUser()->find($id);
        }

        public function create($data)
        {
                return $this->journal->create($data);
        }

        public function update($id, $data)
        {
                $entry = $this->journal->curUser()->find($id);
                if (!$entry) {
                        return false;
                }

                $entry->title = $data['title'];
                $entry->date = $data['date'];
                $entry->entry = $data['entry'];

                return $entry->save();
        }

        public function destroy($id)
        {
                $entry = $this->journal->curUser()->find($id);
                if (!$entry) {
                        return false;
                }
                return $entry->delete();
        }
}
