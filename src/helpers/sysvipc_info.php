<?php

namespace Takuya\SysV;

use function PHPUnit\Framework\matches;
use function PHPUnit\Framework\containsEqual;

if( ! function_exists('sysv_info') ) {
  require_once __DIR__.'/../SysV/IPCInfo.php';
  require_once __DIR__.'/../SysV/IPCInfoEntry.php';
  require_once __DIR__.'/../SysV/IPCInfoEntry.php';
  define("SYSV_INFO_SEM", 1);
  define("SYSV_INFO_MSG", 2);
  define("SYSV_INFO_SHM", 4);
  function sysvipc_info( int $info_variable = SYSV_INFO_SEM|SYSV_INFO_MSG|SYSV_INFO_SHM,$print=true ) {
    $files = [
      SYSV_INFO_SEM => ["/proc/sysvipc/sem", 'sem'],
      SYSV_INFO_SHM => ["/proc/sysvipc/shm", 'shm'],
      SYSV_INFO_MSG => ["/proc/sysvipc/msg", 'msg'],
    ];
    $ret = [];
    foreach ($files as $k => [$file, $name]) {
      if( ! ( $k&$info_variable ) ) {
        continue;
      }
      $lines = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
      if( ! $lines ) {
        die("Failed to read $file\n");
      }
      $header = preg_split('/\s+/', trim(array_shift($lines))); // ヘッダー取得
      $data = [];
      foreach ($lines as $line) {
        $cols = preg_split('/\s+/', trim($line));
        if( count($cols) === count($header) ) {
          $data[] = array_combine($header, $cols);
        }
      }
      $ret[$name]=$data;
    }
    return $ret;
  }
}
if( !empty($argv) && __FILE__ == realpath($argv[0]) ) {
  var_dump(sysvipc_info());
}
