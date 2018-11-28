cd ormroot
mysql -uhomestead --password="secret" --host="127.0.0.1" --port="33060" 4PFoods --execute='FLUSH TABLES'
../application/third_party/propel/generator/bin/propel-gen
../application/third_party/propel/generator/bin/propel-gen insert-sql
mysql -uhomestead --password="secret" --host="127.0.0.1" --port="33060" 4PFoods < structural.sql
cd ../tests
./phpunit --stop-on-failure -v -c phpunit.xml
cd ..
