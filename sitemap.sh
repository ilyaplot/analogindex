#!/bin/sh
cd /home/ilyaplot/analogindex
x=`ps aux | grep -c 'php console.php sitemap')`
if [ $x -eq 2 ]; then
 exit 0
else
 echo "Запускаю процесс"
 php console.php sitemap > /dev/null &
fi

