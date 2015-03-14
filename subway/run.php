<?php

require_once 'SubwayQueue.php';
require_once 'SubwayProcess.php';

class character extends \Ophp\Subway\Process {
     protected $character;
     
     protected $running = false;
     
     protected $position;
     
     protected $sortAdapter;
     
     public function __construct($character)
     {
         $this->character = $character;
     }

     public function getCharacter()
     {
         return $this->character;
     }
     
     public function setSortAdapter($sortAdapter)
     {
         $this->sortAdapter = $sortAdapter;
     }
     
     public function execute()
     {
         echo __CLASS__ . $this->character . "\n";
         
         if (!$this->running) {
             $this->sortAdapter->addCharacter($this);
         }
         if (isset($this->position)) {
             echo "I am '" . $this->character . "' and my alphabetic position is: " . $this->position . "\n";
             return true;
         } else {
             return false;
         }
     }
     
     public function setPosition($pos)
     {
         $this->position = $pos;
     }
     
     /**
      * Returns the position of this character in the string
      */
     public function getPosition()
     {
         return $this->position;
     }

}

class sorter extends \Ophp\Subway\Process {
     protected $length;
     protected $characters = [];
     
     public function __construct($length)
     {
         $this->length = $length;
     }
     
     public function addCharacter($ch)
     {
         $this->characters[] = $ch;
     }
     
     public function execute()
     {
         echo __CLASS__ . $this->length . "\n";
         
         if (count($this->characters) == $this->length) {
             $this->sortCharacters();
             foreach ($this->characters as $pos => $ch) {
                 $ch->setPosition($pos);
             }
             return true;
         } else {
             return false;
         }
         
     }

     public function sortCharacters()
     {
         usort($this->characters, function($a, $b) {
             return ord($a->getCharacter()) - ord($b->getCharacter());
         });
     }

}

$str = "bdace";

$queue = new Ophp\Subway\Queue;

$sorter = new sorter(strlen($str));

$queue->addProcess($sorter);

$chars = [];
foreach (str_split($str) as $ch) {
    $chProc = new character($ch);
    $chProc->setSortAdapter($sorter);
    $chars[] = $chProc;
    $queue->addProcess($chProc);
}

$queue->execute();
