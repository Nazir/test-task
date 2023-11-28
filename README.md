# test-symfony-back

## Technical Requirements & Installation

[PHP 8.2](https://www.php.net/releases/8.2/en.php) - [Installation and Configuration](https://www.php.net/manual/en/install.php)

[Composer (System Requirements)](https://getcomposer.org/doc/00-intro.md#system-requirements) or [The Symfony CLI tool (download)](https://symfony.com/download)

[Symfony 6.3 (Technical Requirements)](https://symfony.com/doc/6.3/setup.html#technical-requirements)

[PostgreSQL 16](https://www.postgresql.org/download/) - [Requirements](https://www.postgresql.org/docs/16/install-requirements.html)

[Symfony Local Web Server](https://symfony.com/doc/5.4/setup/symfony_server.html) or other. I like [nginx](https://nginx.org/ru/) - [minimum configuration](https://symfony.com/doc/current/setup/web_server_configuration.html#nginx)

[Symfony Docker Integration](https://symfony.com/doc/5.4/setup/symfony_server.html#docker-integration) (if needed)

# Important information

1. Do not modify the `.env` file. Make changes at lower levels. For example `.env.dev` и `.env.prod` (if he is).
   And for “fine” settings use `.env.dev.local` and `.env.prod.local`.
   If necessary, run the command `composer dump-env dev` to compile into a single file `.env.local.php`.
   The best thing to do is to understand what you are doing...

2. [In php.ini, try to set `serialize_precision` on `-1`.](https://github.com/symfony/symfony/issues/30488#issuecomment-502353585)

3. API in subfolder `/Postman`. Download [Postman](https://www.postman.com/) & import files from subfolder.

## Settings

#### Copy file `.env.dev` to `.env.dev.local`
```shell
cp .env.dev .env.dev.local
```

##### or `.env.prod.local` for production
```shell
cp .env.prod .env.prod.local
```

#### Edit line in `.env.dev.local` (or `.env.prod.local`):
```conf
DATABASE_URL="postgresql://{{user}}:{{password}}@127.0.0.1:5432/test_{{prod|dev}}?serverVersion=16&charset=utf8"
```

_e.g. {{user}} - `test`; {{password}} - generated password; test\_{{prod|dev}} - `test_dev` name for development database_

#### Make Composer install the project's dependencies into vendor/

```shell
composer install
```


#### Run to compile `.env` files for `development`:
```shell
composer dump-env dev
```

or

#### Run to compile `.env` files for `production`:
```shell
composer dump-env prod
```

## Creating DB

### Creating DB user/role `test`

##### By PostgreSQL Client Applications  [createuser](https://www.postgresql.org/docs/16/app-createuser.html)
```shell
createuser --login --createdb --no-password test
```

OR

##### By PostgreSQL Client Applications [psql](https://www.postgresql.org/docs/16/app-psql.html)
```shell
sudo su postgres
psql
```

##### In [psql](https://www.postgresql.org/docs/16/app-psql.html) execute the script:
```sql
CREATE ROLE test WITH LOGIN PASSWORD '{{password}}' CREATEDB;
```

#### Creating DB & DB structure
```shell
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

##### For check list of migrations run command (if need):

```shell
php bin/console doctrine:migrations:list
```

## Starting Web Server

##### Starting Symfony Local Web Server
```shell
symfony server:start
```
OR

##### Starting other web server (e.g. `nginx` on Linux)
```shell
sudo systemctl start nginx.service
```

## Fill data

#### Fill references:

```shell
php bin/console app:references:fill-data --verbose
```

## Other (if needed)

#### Load fixtures for `development`:

```shell
php bin/console doctrine:fixtures:load
```

#### Load fixtures for `production` (only if the database is empty):

```shell
php bin/console doctrine:fixtures:load --env=prod
```

#### Update fixtures (use parameter `--env=prod` for production)
```shell
php bin/console doctrine:fixtures:load --append
```
