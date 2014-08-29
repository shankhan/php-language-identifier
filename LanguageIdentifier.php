<?php

/**
LanguageIdentifier

Author: Zeeshan M. Khan
Requirement: Work with PHP version 5.3 or above

Use: Create a directory with text files (i.e. English.txt, French.txt, German.txt - I have copied text from different newspaper websites: French - http://www.paris.fr/, German - http://www.spiegel.de/, English - http://www.theguardian.com )
*/

class LanguageIdentifier 
{
  private $keys      = array();
  private $languages = array();
  private $n         = 3;
  
  public function __construct($dir, $n = 3, $ext = "txt")
  {
    $this->n = $n;
    $dataFiles = scandir($dir);
    
    if ($dataFiles) {
      foreach($dataFiles AS $file) {
        if ($file == '.' || $file == '..') {
          continue;
        }
        
        $filePath = $dir . '/' . $file;
        $fileInfo = pathinfo($filePath);
        if (@$fileInfo['extension'] != $ext) {
          continue;
        }
        
        $language = @$fileInfo['filename'];
        
        $this->languages["{$language}"] = @$this->languages["{$language}"] ?: 0;
    
        $text = file_get_contents($filePath);
        $words = $this->generateWords($text);
        
        foreach($words as $word) {
          $ngrams = $this->generateNgrams($word);
          
          foreach($ngrams as $ngram) {
            $this->keys["{$ngram}"]                = @$this->keys["{$ngram}"] ?: array();
            $this->keys["{$ngram}"]["{$language}"] = (@$this->keys["{$ngram}"]["{$language}"] ?: 0) + 1;
          }
          
          $this->languages["{$language}"] += count($ngrams);
        }
        
        // var_dump($this->languages);
        // var_dump($this->keys);
      }
    }
  }
  
  public function check($text)
  {
    if (! $this->languages || ! $this->keys) {
      // LOG ERROR - Oops I am not trained for any language yet :(
      return NULL;
    }
    
    $ngrams = array();
    $words  = $this->generateWords($text);
    foreach($words as $word) {
      $_ngrams = $this->generateNgrams($word);
      foreach($_ngrams as $ngram) {
        $ngrams["{$ngram}"] = (@$ngrams["{$ngram}"] ?: 0) + 1;
      }
    }
    
    $totalNGrams = array_sum($ngrams);
    
    $nGramScores = array();
    foreach($ngrams as $ngram => $total) {
      if(! @$this->keys["{$ngram}"]) {
        continue;
      }
      
      foreach($this->keys["{$ngram}"] as $language => $count) {
        $score = ($count / $this->languages["{$language}"]) * ($total / $totalNGrams);
        
        $nGramScores["{$language}"] = (@$nGramScores["{$language}"] ?: 0) + $score;
      }
    }
    
    arsort($nGramScores);
    return key($nGramScores);
  }
  
  protected function generateWords($text)
  {
    $text = strtolower($text);
    preg_match_all('/[a-z-]+/', $text, $matches);
    return $matches[0];
  }
  
  protected function generateNgrams($word)
  {
    $ngrams = array();
    $len    = strlen($word);
    $index  = 0;
    for($i = 0; $i < $len; $i++) {
      if ($i <= ($this->n - 2)) {
        continue;
      }
      
      $ngrams[$index] = '';
      for($j = $this->n - 1; $j >= 0; $j--) {
        $ngrams[$index] .= $word[$i-$j];
      }
      
      $index++;
    }
    
    return $ngrams;
  }    
}
