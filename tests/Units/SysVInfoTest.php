<?php

namespace Tests\Units;

use Tests\TestCase;
use Takuya\SysV\IPCInfo;
use function Takuya\Helpers\str_rand;
use function Takuya\SysV\sysvipc_info;

class SysVInfoTest extends TestCase {
  
  public function test_sysvipc_info_show() {
    try {
      $sem = sem_get($sem_key = IPCInfo::ipc_key(str_rand()));
      $msg = msg_get_queue($msg_key = IPCInfo::ipc_key(str_rand()));
      $shm = shm_attach($shm_key = IPCInfo::ipc_key(str_rand()));
      sem_acquire($sem);
      msg_send($msg, rand(), str_rand());
      shm_put_var($shm, rand(), str_rand());
      sem_release($sem);
      $str = (string)new IPCInfo;
      $this->assertStringContainsString("# sem\n", $str);
      $this->assertStringContainsString("# msg\n", $str);
      $this->assertStringContainsString("# shm\n", $str);
      $this->assertStringContainsString("# shm\n", $str);
      $this->assertStringContainsString(sprintf("0x%08x", $sem_key), $str);
      $this->assertStringContainsString(sprintf("0x%08x", $msg_key), $str);
      $this->assertStringContainsString(sprintf("0x%08x", $shm_key), $str);
    } finally {
      try {
        @sem_remove($sem);
        @msg_remove_queue($msg);
        @shm_remove($shm);
      } catch (\Error) {
        // ignore.
      }
    }
  }
  
  public function test_sysvipc_remove() {
    try {
      $sem = sem_get($sem_key = IPCInfo::ipc_key($sem_name = str_rand()));
      $msg = msg_get_queue($msg_key = IPCInfo::ipc_key($msg_name = str_rand()));
      $shm = shm_attach($shm_key = IPCInfo::ipc_key($shm_name = str_rand()));
      sem_acquire($sem);
      sem_release($sem);
      msg_send($msg, rand(), str_rand());
      shm_put_var($shm, rand(), str_rand());
      shm_detach($shm);
      $manger = new IPCInfo();
      $manger->remove(IPCInfo::ipc_key($sem_name));
      $manger->remove(IPCInfo::ipc_key($msg_name));
      $manger->remove(IPCInfo::ipc_key($shm_name));
      $str = $manger->show();
      $this->assertStringNotContainsString(sprintf("0x%8x", $sem_key), $str);
      $this->assertStringNotContainsString(sprintf("0x%8x", $msg_key), $str);
      $this->assertStringNotContainsString(sprintf("0x%8x", $shm_key), $str);
    } finally {
      try {
        @sem_remove($sem);
        @msg_remove_queue($msg);
        @shm_remove($shm);
      } catch (\Error) {
        // ignore.
      }
    }
  }
}