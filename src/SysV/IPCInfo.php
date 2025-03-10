<?php

namespace Takuya\SysV;

use Takuya\ProcOpen\ProcOpen;

class IPCInfo implements \Stringable {
  
  protected int $info_flag;
  
  public static function ipc_key( string $uniq_name ) {
    return crc32($uniq_name)&0x7FFFFFFF;
  }
  
  private ?array $info;
  
  public function __construct( $info_variables = SYSV_INFO_SEM|SYSV_INFO_MSG|SYSV_INFO_SHM ) {
    $this->info_flag = $info_variables;
    $this->reload();
  }
  public function reload():void{
    $this->info = sysvipc_info($this->info_flag);
  }
  
  
  public function remove( int $ipckey_int ):bool {
    $found = $this->find($ipckey_int);
    $cat = $found['category'];
    $ret = match ( $cat ) {
      'shm' => ( fn() => shm_remove(shm_attach($ipckey_int)) )(),
      'msg' => ( fn() => msg_remove_queue(msg_get_queue($ipckey_int)) )(),
      'sem' => ( fn() => sem_remove(sem_get($ipckey_int)) )(),
      default => false,
    };
    $this->reload();
    
    return $ret;
  }
  
  public function find( int $ipckey_int ) {
    
    foreach ($this->info as $category => $lines) {
      foreach ($lines as $line) {
        if( ( $line['key'] ?? -1 ) == $ipckey_int ) {
          return ['category' => $category, 'entry' => $line];
        }
      }
    }
    
    return null;
  }
  
  public function toArray() {
    return $this->info;
  }
  
  public function show():string {
    $lines = [];
    foreach ($this->info as $type_name => $type) {
      $lines[] = "# {$type_name}\n";
      foreach ($type as $idx => $value) {
        if( $idx == 0 ) {
          $lines [] = ( new IPCInfoEntry($value) )->header();
        }
        $lines [] = (string)( new IPCInfoEntry($value) );
      }
      $lines[] = "\n";
    }
    
    return implode("", $lines);
  }
  
  public function __toString():string {
    return $this->show();
  }
}