<?php namespace Smartgoalz\Repositories\Goal;

use Goal;
use Smartgoalz\Repositories\BaseRepository;

class EloquentGoalRepository extends BaseRepository implements GoalRepository {

        protected $goal;

        public function __construct(Goal $goal)
        {
                parent::__construct($goal);
                $this->goal = $goal;
        }

        public function getAll()
        {
                return $this->goal->curUser()->orderBy('title', 'DESC')->get();
        }

        public function get($id)
        {
                return $this->goal->curUser()->find($id);
        }

        public function create($data)
        {
                return $this->goal->create($data);
        }

        public function update($id, $data)
        {
                $goal = $this->goal->curUser()->find($id);
                if (!$goal) {
                        return false;
                }

                $goal->title = $data['title'];
                $goal->start_date = $data['start_date'];
                $goal->due_date = $data['due_date'];
                $goal->difficulty = $data['difficulty'];
                $goal->priority = $data['priority'];
                $goal->reason = $data['reason'];

                return $goal->save();
        }

        public function destroy($id)
        {
                $goal = $this->goal->curUser()->find($id);
                if (!$goal) {
                        return false;
                }
                return $goal->delete();
        }
}
