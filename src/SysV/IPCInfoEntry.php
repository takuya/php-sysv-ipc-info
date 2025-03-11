<?php

namespace Takuya\SysV;

use function PHPUnit\Framework\matches;

class IPCInfoEntry implements \Stringable {
  
  public function __construct( public array $values ) {
  }
  
  public function getIPCKeyHex() {
    return sprintf('0x%08x', $this->values['key']);
  }
  
  public function getOwner() {
    return posix_getpwuid($this->uid);
  }
  
  protected function formatSize( $size ):string {
    $units = ['B', 'K', 'M', 'G', 'T', 'P']; // 2^10 (1024) ごとに単位が変わる
    for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
      $size /= 1024;
    }
    
    return number_format($size, ( $i === 0 ? 0 : 1 )).$units[$i];
  }
  
  public function toArray():array {
    return ['category' => $this->category, 'entry' => $this->values];
  }
  
  public function colsize() {
    $keys = array_intersect(
      array_keys(
        $cols = [
          'key'    => 10,
          'uid'    => 13,
          'gid'    => 15,
          'perms'  => 8,
          'size'   => 6,
          'qnum'   => 6,
          'cbytes' => 8,
          'nattch' => 8,
          'otime'  => 13,
          'atime'  => 13,
          'dtime'  => 13,
          'ctime'  => 13,
          'rss'    => 6,
          'swap'   => 6,
        ]),
      array_keys($this->values));
    
    return array_intersect_key($cols, array_flip($keys));
  }
  
  public function header():string {
    $str = '';
    foreach ($this->colsize() as $name => $l) {
      $str .= sprintf("% {$l}s", $name);
    }
    
    return $str."\n";
  }
  
  public function __get( $name ) {
    if( ! isset($this->values[$name]) ) {
      return null;
    }
    if( preg_match('/time/', $name) > 0 ) {
      $name = ucfirst($name);
      
      return $this->{"get{$name}"}();
    }
    
    return match ( $name ) {
      'key' => $this->getIPCKeyHex(),
      'size' => $this->formatSize($this->values[$name]),
      'cbytes' => $this->formatSize($this->values[$name]),
      'swap' => $this->formatSize($this->values[$name]),
      'rss' => $this->formatSize($this->values[$name]),
      'uid' => posix_getpwuid($this->values[$name])['name'],
      'gid' => posix_getgrgid($this->values[$name])['name'],
      default => $this->values[$name]
    };
  }
  
  public function __call( string $name, array $arguments ) {
    if( preg_match('/get((.+)time)$/', $name, $m) !== false ) {
      $key = strtolower($m[1]);
      if( $this->values[$key] ) {
        return ( new \DateTime() )->setTimestamp($this->values[$key])->format('m-d H:i');
      }
      
      return null;
    }
    throw new \BadFunctionCallException($name);
  }
  
  public function __toString():string {
    
    $str = '';
    $cols = array_intersect(array_keys($this->colsize()), array_keys($this->values));
    foreach ($cols as $k) {
      $len = $this->colsize()[$k];
      $str .= sprintf("% {$len}s", $this->{$k});
    }
    
    return $str."\n";
  }
}