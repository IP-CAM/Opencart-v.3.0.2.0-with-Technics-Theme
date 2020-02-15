.PHONY: mysql dump

PWD_DIR = $(shell pwd)

MYSQL_CONTAINER_NAME = stanok-mysql
DB_NAME = oc_stanok_db
MYSQL_USER = root
MYSQL_PASS = root

EXEC_MYSQL = docker exec -it $(MYSQL_CONTAINER_NAME) bash

mysql:
	$(EXEC_MYSQL)

dump:
	mysqldump -u $(MYSQL_USER) -p$(MYSQL_PASS) $(DB_NAME) > dump/new_dump.sql
