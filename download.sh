#/bin/sh
cd /home/ilyaplot/analogindex
#lists
php console.php download smartphoneuaList --source=1
php console.php download smartphoneuaList --source=2
php console.php download smartphoneuaList --source=3
php console.php download antutu --type=1
php console.php download antutu --type=2
php console.php download gsmarenalist

#download
php console.php download smartphoneua
php console.php download gsmarena


#parse
php console.php parse gsmarena
php console.php parse smartphoneua
