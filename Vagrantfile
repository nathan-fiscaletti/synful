# Vagrant file for a Synful Test Environment Box
# Requires bento/ubuntu-16.04 vagrant box and virtualbox provider
# To import the bento/ubuntu-16.04 box run `vagrant init bento/ubuntu-16.04`
# 
# For more information on using the Vagrant Development Environment, 
# see https://github.com/nathan-fiscaletti/synful/wiki/Using-Vagrant

Vagrant.configure("2") do |config|

  config.vm.box = "bento/ubuntu-16.04"

  config.vm.network "public_network"

  config.vm.synced_folder "./", "/var/www/html", create: true, group: "www-data", owner: "www-data"

  config.vm.provider "virtualbox" do |v|
    v.name = "Synful Test Box"
    v.customize ["modifyvm", :id, "--memory", "1024"]
  end

  $script = <<-SCRIPT

  echo "Running apt-get update..."
  sudo apt-get update -y > /dev/null 2>&1

  echo "Installing Dependencies..."
  sudo apt-get -y install php7.0-cli php7.0-zip unzip apache2 php7.0 libapache2-mod-php7.0 php7.0-mysql php7.0-mcrypt composer mysql-client > /dev/null 2>&1

  echo "Installing MySQL..."
  sudo apt-get install debconf-utils -y > /dev/null 2>&1
  debconf-set-selections <<< "mysql-server mysql-server/root_password password password" > /dev/null 2>&1
  debconf-set-selections <<< "mysql-server mysql-server/root_password_again password password" > /dev/null 2>&1
  sudo apt-get -y install mysql-server > /dev/null 2>&1

  echo "Creating database..."
  mysql -u root -ppassword -e "CREATE DATABASE synful" >/dev/null 2>&1

  echo "Making MySql accessible over network..."
  sed -i '43s!127.0.0.1!0.0.0.0!' /etc/mysql/mysql.conf.d/mysqld.cnf

  echo "Restarting MySql Service..."
  service mysql restart

  echo "Updating SQL Permissioins..."
  mysql -u root -ppassword -e "USE mysql;UPDATE user SET host='%' WHERE User='root';GRANT ALL ON *.* TO 'root'@'%';FLUSH PRIVILEGES;" >/dev/null 2>&1

  echo "Removing placeholder index file..."
  rm /var/www/html/index.html

  echo "Setting site root..."
  sed -i '12s!/var/www/html!/var/www/html/public!' /etc/apache2/sites-enabled/000-default.conf
  sed -i '164s!/var/www!/var/www/html/public!' /etc/apache2/apache2.conf

  echo "Enabling MCrypt..."
  sed -i '1718imcrypt.so' /etc/php/7.0/apache2/php.ini
  sed -i '1718imcrypt.so' /etc/php/7.0/cli/php.ini 

  echo "Restarting Apache Service..."
  service apache2 restart

  echo "Running composer install for Synful..."
  cd /var/www/html/
  composer install --no-scripts >/dev/null 2>&1

  echo "Creating default MySql tables..."
  ./synful -ct

  echo "Done!"
  echo "MySql Credentials: [ Username = root, Password = password, Database = synful ]"
  echo "You can access your Synful API at one of the following addresses:";for ip in $( hostname -I ); do echo "    http://"$ip"/"; done;
  echo "A good next step would be to change your Synful Database Definition's host value to use the IP of the vagrant box."
  echo "It will still work without this, but changing it will allow you to run Synful from CLI on your host machine"
  echo "and still have the changes effect the database on the Vagrant Box."

  SCRIPT

  config.vm.provision "shell", inline: $script

end
