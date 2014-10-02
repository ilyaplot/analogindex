#!/bin/sh
#exit 0
cd /home/ilyaplot/analogindex
x=`ps aux | grep -c 'php console.php resize')`
if [ $x -gt 1 ]; then
 exit 0
else
 echo "Запускаю процесс"
 php console.php resize > /dev/null &
fi

