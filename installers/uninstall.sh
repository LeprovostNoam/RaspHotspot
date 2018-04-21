#!/bin/bash
RaspHotspot_dir="/etc/RaspHotspot"
RaspHotspot_user="www-data"
version=`sed 's/\..*//' /etc/debian_version`

# Determine version and set default home location for lighttpd 
if [ $version -ge 8 ]; then
    version_msg="Raspian version 8.0 or later"
    webroot_dir="/var/www/html"
else
    version_msg="Raspian version earlier than 8.0"
    webroot_dir="/var/www"
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

# Checks to make sure uninstallation info is correct
function config_uninstallation() {
    install_log "Configure installation"
    echo "Detected ${version_msg}" 
    echo "Install directory: ${RaspHotspot_dir}"
    echo "Lighttpd directory: ${webroot_dir}"
    echo -n "Uninstall RaspHotspot with these values? [y/N]: "
    read answer
    if [[ $answer != "y" ]]; then
        echo "Installation aborted."
        exit 0
    fi
}

# Checks for/restore backup files
function check_for_backups() {
    if [ -d "$RaspHotspot_dir/backups" ]; then
        if [ -f "$RaspHotspot_dir/backups/interfaces" ]; then
            echo -n "Restore the last interfaces file? [y/N]: "
            read answer
            if [[ $answer -eq 'y' ]]; then
                sudo cp "$RaspHotspot_dir/backups/interfaces" /etc/network/interfaces
            fi
        fi
        if [ -f "$RaspHotspot_dir/backups/hostapd.conf" ]; then
            echo -n "Restore the last hostapd configuration file? [y/N]: "
            read answer
            if [[ $answer -eq 'y' ]]; then
                sudo cp "$RaspHotspot_dir/backups/hostapd.conf" /etc/hostapd/hostapd.conf
            fi
        fi
        if [ -f "$RaspHotspot_dir/backups/dnsmasq.conf" ]; then
            echo -n "Restore the last dnsmasq configuration file? [y/N]: "
            read answer
            if [[ $answer -eq 'y' ]]; then
                sudo cp "$RaspHotspot_dir/backups/dnsmasq.conf" /etc/dnsmasq.conf
            fi
        fi
        if [ -f "$RaspHotspot_dir/backups/dhcpcd.conf" ]; then
            echo -n "Restore the last dhcpcd.conf file? [y/N]: "
            read answer
            if [[ $answer -eq 'y' ]]; then
                sudo cp "$RaspHotspot_dir/backups/dhcpcd.conf" /etc/dhcpcd.conf
            fi
        fi
        if [ -f "$RaspHotspot_dir/backups/rc.local" ]; then
            echo -n "Restore the last rc.local file? [y/N]: "
            read answer
            if [[ $answer -eq 'y' ]]; then
                sudo cp "$RaspHotspot_dir/backups/rc.local" /etc/rc.local
            else
                echo -n "Remove RaspHotspot Lines from /etc/rc.local? [Y/n]: "
                if $answer -ne 'n' ]]; then
                    sed -i '/#RaspHotspot/d' /etc/rc.local
                fi
            fi
        fi
    fi
}

# Removes RaspHotspot directories
function remove_RaspHotspot_directories() {
    install_log "Removing RaspHotspot Directories"
    if [ ! -d "$RaspHotspot_dir" ]; then
        install_error "RaspHotspot Configuration directory not found. Exiting!"
    fi

    if [ ! -d "$webroot_dir" ]; then
        install_error "RaspHotspot Installation directory not found. Exiting!"
    fi

    sudo rm -rf "$webroot_dir"/*
    sudo rm -rf "$RaspHotspot_dir"

}

# Removes www-data from sudoers
function clean_sudoers() {
    # should this check for only our commands?
    sudo sed -i '/www-data/d' /etc/sudoers
}

function remove_RaspHotspot() {
    config_uninstallation
    check_for_backups
    remove_RaspHotspot_directories
    clean_sudoers
}

remove_RaspHotspot
