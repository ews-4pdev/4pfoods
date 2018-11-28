cd ormroot
../application/third_party/propel/generator/bin/propel-gen
../application/third_party/propel/generator/bin/propel-gen insert-sql
mysql -uroot -proot LoKohlrabi < structural.sql
cd ../tests
phpunit --stop-on-failure -v -c phpunit_stripe.xml
cd ..
