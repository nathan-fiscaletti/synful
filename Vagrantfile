# Vagrant file for a Synful Test Environment Box
# Requires virtualbox provider
Vagrant.configure("2") do |config|

  config.vm.box = "bento/ubuntu-16.04"

  config.vm.network "public_network"

  config.vm.synced_folder "./", "/var/www/html", create: true, group: "www-data", owner: "www-data"

  config.vm.provider "virtualbox" do |v|
    v.name = "Synful Test Box"
    v.customize ["modifyvm", :id, "--memory", "1024"]
  end

  $script = <<-SCRIPT
  echo "Updating packages..."
  apt-get -y update >/dev/null 2>&1

  echo "Adding repo lists..."
  apt-get install python-software-properties -y >/dev/null 2>&1
  add-apt-repository ppa:ondrej/php -y >/dev/null 2>&1
  apt-get -y update >/dev/null 2>&1
  echo "Installing Dependencies..."
  apt-get install -y php7.2-cli composer php7.2-zip unzip apache2 php7.2 libapache2-mod-php7.2 php7.2-mysql php7.2-mbstring mysql-client apache2-utils php-apcu >/dev/null 2>&1
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
  echo "Enabling modrewrite..."
  a2enmod rewrite
  sed -i '155s!None!All!' /etc/apache2/apache2.conf
  sed -i '166s!None!All!' /etc/apache2/apache2.conf
  echo "Restarting Apache Service..."
  service apache2 restart
  echo "Installing Synful..."
  cd /var/www/html/
  ./synful install >/dev/null 2>&1
  echo "Done!"
  echo "MySql Credentials: [ Username = root, Password = password, Database = synful ]"
  echo "You can access your Synful API at one of the following addresses:";for ip in $( hostname -I ); do echo "    http://"$ip"/"; done;
  echo "A good next step would be to change your Synful Database Definition's host value to use the IP of the vagrant box."
  echo "It will still work without this, but changing it will allow you to run Synful from CLI on your host machine"
  echo "and still have the changes effect the database on the Vagrant Box."
  SCRIPT

  config.vm.provision "shell", inline: $script

end