<?php namespace Smartgoalz\Presenters;

use McCool\LaravelAutoPresenter\BasePresenter;
use Journal;

class JournalPresenter extends BasePresenter
{
        public function __construct(Journal $journal)
        {
                $this->resource = $journal;
        }

        public function dateme(Journal $journal)
        {
                return 'Hello, ' . $this->resource->date . ' I am the presenter';
        }
}
