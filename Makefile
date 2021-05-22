login:##login binance-laravel
	docker-compose exec binance-laravel /bin/bash

permissions:##doesn't work atm hmmm
	docker-compose exec binance-laravel /bin/bash -c "chown -R 1001:users /var/www/binance"
#artisan:
#	docker-compose exec binance-laravel /bin/bash -c
laravelwatch: ##watch webpack files
	make laravelbuild
	docker-compose exec binance-laravel /bin/bash -c "npm run watch"

laravelbuild: ##build webpack files
	docker-compose exec binance-laravel /bin/bash -c "npm run dev"
