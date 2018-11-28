mysqldump -umystage4pu --password="Qmm4t?66" staging4p > current.sql
mysql -umystage4pu --password="Qmm4t?66" staging4p --execute='FLUSH TABLES'
./pgen.sh
./psql.sh
