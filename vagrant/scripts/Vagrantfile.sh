#filename: Vagrantfile.provision.sh
#!/usr/bin/env bash

# ---------------------------------------------------------------------------------------------------------------------
# Variables & Functions
# ---------------------------------------------------------------------------------------------------------------------
APP_DATABASE_NAME='app'

echoTitle () {
    echo -e "\033[0;30m\033[42m -- $1 -- \033[0m"
}



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Virtual Machine Setup'
# ---------------------------------------------------------------------------------------------------------------------
# Update packages
apt-get update -qq
apt-get -y install git curl vim



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Installing and Setting: Apache'
# ---------------------------------------------------------------------------------------------------------------------
# Install packages
apt-get install -y apache2 libapache2-mod-fastcgi apache2-mpm-worker

# linking Vagrant directory to Apache 2.4 public directory
# rm -rf /var/www
# ln -fs /vagrant /var/www

# Add ServerName to httpd.conf
echo "ServerName localhost" > /etc/apache2/httpd.conf

# Setup hosts file
VHOST=$(cat <<EOF
    <VirtualHost *:80>
      DocumentRoot "/var/www/app/public"
      ServerName app.dev
      ServerAlias app.dev
      <Directory "/var/www/app/public">
        AllowOverride All
        Require all granted
      </Directory>
    </VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-enabled/000-default.conf

# Loading needed modules to make apache work
a2enmod actions fastcgi rewrite
sudo service apache2 restart


# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Installing: PHP'
# ---------------------------------------------------------------------------------------------------------------------
# Add repository
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y python-software-properties software-properties-common

# Remove PHP5
# apt-get purge php5-fpm -y
# apt-get --purge autoremove -y

# Install packages
apt-get install -y php7.1 php7.1-fpm
apt-get install -y php7.1-mysql
apt-get install -y mcrypt php7.1-mcrypt
apt-get install -y php7.1-cli php7.1-curl php7.1-mbstring php7.1-xml php7.1-mysql
apt-get install -y php7.1-json php7.1-cgi php7.1-gd php-imagick php7.1-bz2 php7.1-zip



# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Setting: PHP with Apache'
# ---------------------------------------------------------------------------------------------------------------------
apt-get install -y libapache2-mod-php7.1

# Enable php modules
# php71enmod mcrypt (error)

# Trigger changes in apache
a2enconf php7.1-fpm
sudo service apache2 reload

# Packages Available:
# apt-cache search php7-*



# ---------------------------------------------------------------------------------------------------------------------
# echoTitle 'Installing & Setting: X-Debug'
# ---------------------------------------------------------------------------------------------------------------------
# cat << EOF | sudo tee -a /etc/php/7.1/mods-available/xdebug.ini
# xdebug.scream=1
# xdebug.cli_color=1
# xdebug.show_local_vars=1
# EOF



# ---------------------------------------------------------------------------------------------------------------------
# Others
# ---------------------------------------------------------------------------------------------------------------------
echoTitle 'Installing: Node 6 and update NPM'
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
apt-get install -y nodejs
npm install npm@latest -g

echoTitle 'Installing: Git'
apt-get install -y git

echoTitle 'Installing: Composer'
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer



# ---------------------------------------------------------------------------------------------------------------------
# Others
# ---------------------------------------------------------------------------------------------------------------------
# Output success message
echoTitle "Your machine has been provisioned"
echo "-------------------------------------------"
echo "MySQL is available on port 3306 with username 'root' and password 'password'"
echo "(you have to use 127.0.0.1 as opposed to 'localhost')"
echo "Apache is available on port 80"
echo -e "Head over to http://192.168.100.100 to get started"