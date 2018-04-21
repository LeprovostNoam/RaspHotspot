UPDATE_URL="https://raw.githubusercontent.com/LeprovostNoam/RaspHotspot/master/"
wget -q ${UPDATE_URL}/installers/common.sh -O /tmp/RaspHotspotcommon.sh
source /tmp/RaspHotspotcommon.sh && rm -f /tmp/RaspHotspotcommon.sh

function update_system_packages() {
    install_log "Updating sources"
    sudo apt-get update || install_error "Unable to update package list"
}

function install_dependencies() {
    install_log "Installing required packages"
    sudo apt-get install lighttpd $php_package git hostapd dnsmasq screen php php-curl|| install_error "Unable to install dependencies"
}

install_RaspHotspot
