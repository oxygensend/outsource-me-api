echo >&3 "$0: Deploy"

cd /var/www/html

bin/console doctrine:database:create --if-not-exists --no-interaction
bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:clear


# test
bin/console doctrine:database:create --if-not-exists --no-interaction --env test
bin/console doctrine:migrations:migrate --no-interaction --env test
bin/console hautelook:fixtures:load --no-interaction --env=test


# generate JWT key pairs
bin/console lexik:jwt:generate-keypair --skip-if-exists