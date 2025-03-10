# php-sysv-ipc-info 
  
This package manage sysvipc( `/proc/sysvipc/{shm,msg,sem}` ).

## Installing 
from Packagist 
```shell
composer require takuya/php-sysv-ipc-info 
```
from GitHub
```shell
name='takuya/php-sysv-ipc-info'
repo=git@github.com:$name.git
composer config repositories.$name vcs $repo
composer require $name:master
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



