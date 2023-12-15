  #!/bin/bash
echo "====================== == ========================"
git pull
echo "====================== LB ========================"
php -d memory_limit=-1 yii migrate --interactive=0
php -d memory_limit=-1 yii migratesync --interactive=0
php yii access/import
php yii setting/update
php yii cache/flush-all
