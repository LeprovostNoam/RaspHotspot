RaspHotspot_dir="/etc/RaspHotspot"
RaspHotspot_user="www-data"
version=`sed 's/\..*//' /etc/debian_version`

# Determine version, set default home location for lighttpd and 
# php package to install 
webroot_dir="/var/www/html" 
if [ $version -eq 9 ]; then 
    version_msg="Raspian 9.0 (Stretch)" 
    php_package="php7.0-cgi" 
elif [ $version -eq 8 ]; then 
    version_msg="Raspian 8.0 (Jessie)" 
    php_package="php5-cgi" 
else 
    version_msg="Raspian earlier than 8.0 (Wheezy)"
    webroot_dir="/var/www" 
    php_package="php5-cgi" 
fi 

# Outputs a RaspHotspot Install log line
function install_log() {
    echo -e "\033[1;32mRaspHotspot Install: $*\033[m"
}

# Outputs a RaspHotspot Install Error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mRaspHotspot Install Error: $*\033[m"
    exit 1
}

# Outputs a welcome message
function display_welcome() {
    raspberry='\033[0;35m'
    green='\033[1;32m'

    echo -e "${raspberry}\n"
    echo -e "  ____                 _   _       _                   _  " 
    echo -e " |  _ \ __ _ ___ _ __ | | | | ___ | |_ ___ _ __   ___ | |_ " 
    echo -e " | |_) / _\` / __| \'_ \| |_| |/ _ \| __/ __| \'_ \ / _ \| __|" 
    echo -e " |  _ < (_| \__ \ |_) |  _  | (_) | |_\__ \ |_) | (_) | |_ " 
    echo -e " |_| \_\__,_|___/ .__/|_| |_|\___/ \__|___/ .__/ \___/ \__|" 
    echo -e "                |_|                       |_|              "                            
    echo -e "${green}"
    echo -e "The Quick Installer will guide you through a few easy steps\n\n"
}

### NOTE: all the below functions are overloadable for system-specific installs
### NOTE: some of the below functions MUST be overloaded due to system-specific installs

function config_installation() {
    install_log "Configure installation"
    echo "Detected ${version_msg}" 
    echo "Install directory: ${RaspHotspot_dir}"
    echo "Lighttpd directory: ${webroot_dir}"
    echo -n "Complete installation with these values? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo "Installation aborted."
        exit 0
    fi
}

# Runs a system software update to make sure we're using all fresh packages
function update_system_packages() {
    # OVERLOAD THIS
    install_error "No function definition for update_system_packages"
}

# Installs additional dependencies using system package manager
function install_dependencies() {
    # OVERLOAD THIS
    install_error "No function definition for install_dependencies"
}

# Enables PHP for lighttpd and restarts service for settings to take effect
function enable_php_lighttpd() {
    install_log "Enabling PHP for lighttpd"

    sudo lighttpd-enable-mod fastcgi-php    
    sudo service lighttpd force-reload
    sudo /etc/init.d/lighttpd restart || install_error "Unable to restart lighttpd"
}

# Verifies existence and permissions of RaspHotspot directory
function create_RaspHotspot_directories() {
    install_log "Creating RaspHotspot directories"
    if [ -d "$RaspHotspot_dir" ]; then
        sudo mv $RaspHotspot_dir "$RaspHotspot_dir.`date +%F-%R`" || install_error "Unable to move old '$RaspHotspot_dir' out of the way"
    fi
    sudo mkdir -p "$RaspHotspot_dir" || install_error "Unable to create directory '$RaspHotspot_dir'"

    # Create a directory for existing file backups.
    sudo mkdir -p "$RaspHotspot_dir/backups"

    # Create a directory to store networking configs
    sudo mkdir -p "$RaspHotspot_dir/networking"
    # Copy existing dhcpcd.conf to use as base config
    cat /etc/dhcpcd.conf | sudo tee -a /etc/RaspHotspot/networking/defaults

    sudo chown -R $RaspHotspot_user:$RaspHotspot_user "$RaspHotspot_dir" || install_error "Unable to change file ownership for '$RaspHotspot_dir'"
}

# Generate logging enable/disable files for hostapd
function create_logging_scripts() {
    install_log "Creating logging scripts"
    sudo mkdir $RaspHotspot_dir/hostapd || install_error "Unable to create directory '$RaspHotspot_dir/hostapd'"

    # Move existing shell scripts 
    sudo mv $webroot_dir/installers/*log.sh $RaspHotspot_dir/hostapd || install_error "Unable to move logging scripts"
}

# Generate logging enable/disable files for hostapd
function create_logging_scripts() {
    sudo mkdir /etc/RaspHotspot/hostapd
    sudo mv /var/www/html/installers/*log.sh /etc/RaspHotspot/hostapd
}

# Fetches latest files from github to webroot
function download_latest_files() {
    if [ -d "$webroot_dir" ]; then
        sudo mv $webroot_dir "$webroot_dir.`date +%F-%R`" || install_error "Unable to remove old webroot directory"
    fi

    install_log "Cloning latest files from github"
    git clone https://github.com/LeprovostNoam/RaspHotspot /tmp/RaspHotspotwebgui || install_error "Unable to download files from github"
    sudo mv /tmp/RaspHotspotwebgui $webroot_dir || install_error "Unable to move RaspHotspot to web root" 
	chmod +x $webroot_dir/_inc/idents.php
	chmod +x $webroot_dir/_inc/version.php
}
  
# Sets files ownership in web root directory
function change_file_ownership() {
    if [ ! -d "$webroot_dir" ]; then
        install_error "Web root directory doesn't exist"
    fi

    install_log "Changing file ownership in web root directory"
    sudo chown -R $RaspHotspot_user:$RaspHotspot_user "$webroot_dir" || install_error "Unable to change file ownership for '$webroot_dir'"
}

# Check for existing /etc/network/interfaces and /etc/hostapd/hostapd.conf files
function check_for_old_configs() {
    if [ -f /etc/network/interfaces ]; then
        sudo cp /etc/network/interfaces "$RaspHotspot_dir/backups/interfaces.`date +%F-%R`"
        sudo ln -sf "$RaspHotspot_dir/backups/interfaces.`date +%F-%R`" "$RaspHotspot_dir/backups/interfaces"
    fi

    if [ -f /etc/hostapd/hostapd.conf ]; then
        sudo cp /etc/hostapd/hostapd.conf "$RaspHotspot_dir/backups/hostapd.conf.`date +%F-%R`"
        sudo ln -sf "$RaspHotspot_dir/backups/hostapd.conf.`date +%F-%R`" "$RaspHotspot_dir/backups/hostapd.conf"
    fi

    if [ -f /etc/dnsmasq.conf ]; then
        sudo cp /etc/dnsmasq.conf "$RaspHotspot_dir/backups/dnsmasq.conf.`date +%F-%R`"
        sudo ln -sf "$RaspHotspot_dir/backups/dnsmasq.conf.`date +%F-%R`" "$RaspHotspot_dir/backups/dnsmasq.conf"
    fi

    if [ -f /etc/dhcpcd.conf ]; then
        sudo cp /etc/dhcpcd.conf "$RaspHotspot_dir/backups/dhcpcd.conf.`date +%F-%R`"
        sudo ln -sf "$RaspHotspot_dir/backups/dhcpcd.conf.`date +%F-%R`" "$RaspHotspot_dir/backups/dhcpcd.conf"
    fi

    if [ -f /etc/rc.local ]; then
        sudo cp /etc/rc.local "$RaspHotspot_dir/backups/rc.local.`date +%F-%R`"
        sudo ln -sf "$RaspHotspot_dir/backups/rc.local.`date +%F-%R`" "$RaspHotspot_dir/backups/rc.local"
    fi
}

# Move configuration file to the correct location
function move_config_file() {
    if [ ! -d "$RaspHotspot_dir" ]; then
        install_error "'$RaspHotspot_dir' directory doesn't exist"
    fi

    install_log "Moving configuration file to '$RaspHotspot_dir'"
    sudo chown -R $RaspHotspot_user:$RaspHotspot_user "$RaspHotspot_dir" || install_error "Unable to change file ownership for '$RaspHotspot_dir'"
}

# Set up default configuration
function default_configuration() {
    install_log "Setting up hostapd"
    if [ -f /etc/default/hostapd ]; then
        sudo mv /etc/default/hostapd /tmp/default_hostapd.old || install_error "Unable to remove old /etc/default/hostapd file"
    fi
    sudo mv $webroot_dir/config/default_hostapd /etc/default/hostapd || install_error "Unable to move hostapd defaults file"
    sudo mv $webroot_dir/config/hostapd.conf /etc/hostapd/hostapd.conf || install_error "Unable to move hostapd configuration file"
    sudo mv $webroot_dir/config/dnsmasq.conf /etc/dnsmasq.conf || install_error "Unable to move dnsmasq configuration file"
    sudo mv $webroot_dir/config/dhcpcd.conf /etc/dhcpcd.conf || install_error "Unable to move dhcpcd configuration file"

    # Generate required lines for Rasp AP to place into rc.local file.
    # #RaspHotspot is for removal script
    lines=(
    'echo 1 > \/proc\/sys\/net\/ipv4\/ip_forward #RaspHotspot'
    'iptables -t nat -A POSTROUTING -j MASQUERADE #RaspHotspot'

    )
    
    for line in "${lines[@]}"; do
        if grep "$line" /etc/rc.local > /dev/null; then
            echo "$line: Line already added"
        else
            sudo sed -i "s/^exit 0$/$line\nexit 0/" /etc/rc.local
            echo "Adding line $line"
        fi
    done
}


# Add a single entry to the sudoers file
function sudo_add() {
    sudo bash -c "echo \"www-data ALL=(ALL) NOPASSWD:$1\" | (EDITOR=\"tee -a\" visudo)" \
        || install_error "Unable to patch /etc/sudoers"
}

# Adds www-data user to the sudoers file with restrictions on what the user can execute
function patch_system_files() {
    # add symlink to prevent wpa_cli cmds from breaking with multiple wlan interfaces
    install_log "symlinked wpa_supplicant hooks for multiple wlan interfaces"
    sudo ln -s /usr/share/dhcpcd/hooks/10-wpa_supplicant /etc/dhcp/dhclient-enter-hooks.d/
    # Set commands array
    cmds=(
        "/sbin/ifdown"
        "/sbin/ifup"
        "/bin/cat /etc/wpa_supplicant/wpa_supplicant.conf"
        "/bin/cat /etc/wpa_supplicant/wpa_supplicant-wlan0.conf"
        "/bin/cat /etc/wpa_supplicant/wpa_supplicant-wlan1.conf"
        "/bin/cp /tmp/wifidata /etc/wpa_supplicant/wpa_supplicant.conf"
        "/bin/cp /tmp/wifidata /etc/wpa_supplicant/wpa_supplicant-wlan0.conf"
        "/bin/cp /tmp/wifidata /etc/wpa_supplicant/wpa_supplicant-wlan1.conf"
        "/sbin/wpa_cli -i wlan0 scan_results"
        "/sbin/wpa_cli -i wlan0 scan"
        "/sbin/wpa_cli reconfigure"
        "/bin/cp /tmp/hostapddata /etc/hostapd/hostapd.conf"
        "/etc/init.d/hostapd start"
        "/etc/init.d/hostapd stop"
        "/etc/init.d/dnsmasq start"
        "/etc/init.d/dnsmasq stop"
        "/bin/cp /tmp/dhcpddata /etc/dnsmasq.conf"
        "/sbin/shutdown -h now"
        "/sbin/reboot"
        "/sbin/ip link set wlan0 down"
        "/sbin/ip link set wlan0 up"
        "/sbin/ip -s a f label wlan0"
        "/sbin/ip link set wlan1 down"
        "/sbin/ip link set wlan1 up"
        "/sbin/ip -s a f label wlan1"
        "/bin/cp /etc/RaspHotspot/networking/dhcpcd.conf /etc/dhcpcd.conf"
        "/etc/RaspHotspot/hostapd/enablelog.sh"
        "/etc/RaspHotspot/hostapd/disablelog.sh"
		"ALL"
    )

    # Check if sudoers needs patching
    if [ $(sudo grep -c www-data /etc/sudoers) -ne 28 ]
    then
        # Sudoers file has incorrect number of commands. Wiping them out.
        install_log "Cleaning sudoers file"
        sudo sed -i '/www-data/d' /etc/sudoers
        install_log "Patching system sudoers file"
        # patch /etc/sudoers file
        for cmd in "${cmds[@]}"
        do
            sudo_add $cmd
            IFS=$'\n'
        done
    else
        install_log "Sudoers file already patched"
    fi
}

function install_complete() {
    install_log "Installation completed!"

    echo -n "The system needs to be rebooted as a final step. Reboot now? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo "Installation aborted."
        exit 0
    fi
    sudo shutdown -r now || install_error "Unable to execute shutdown"
}

function install_RaspHotspot() {
    display_welcome
    config_installation
    update_system_packages
    install_dependencies
    enable_php_lighttpd
    create_RaspHotspot_directories
    check_for_old_configs
    download_latest_files
    change_file_ownership
    create_logging_scripts
    move_config_file
    default_configuration
    patch_system_files
    install_complete
}
