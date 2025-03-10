# php-sysv-ipc-shared-memory 

This package is wrapper for php sysv shm_xxx.

## Installing 
from Packagist 
```shell
composer require takuya/php-sysv-ipc-shared-memory
```
from GitHub
```shell
name='php-sysv-ipc-shared-memory'
composer config repositories.$name \
vcs https://github.com/takuya/$name  
composer require takuya/$name:master
composer install
```
## Special 0x00000000 key

After ipcs removed, but still attached. sysvipc will remain as key=0(0x000000).

## Examples (bin)
```bash
php vendor/bin/sysvipc -l
php vendor/bin/sysvipc -d 0x673df0eb
php vendor/bin/sysvipc -k uniq_name 
```
### helper function
```php
<?php
sysvipc_info(); // => array ['sem'=>[...],'msg'=>[...],'shm'=>[...]]
```

### comparison to ipcs / ipcrm 

Compare to `ipcs` command. This package can return 'atime,ctime'

Compare to `iprm` command. no option (`--semaphore-key`) required, remove first found.



