
#!/bin/bash
webroot_dir="/var/www/html" 

function install_log() {
    echo -e "\033[1;32mRaspHotspot Install: $*\033[m"
}

# Outputs a RaspHotspot Install Error log line and exits with status code 1
function install_error() {
    echo -e "\033[1;37;41mRaspHotspot Install Error: $*\033[m"
    exit 1
}
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
    echo -e "The Quick updater will guide you through a few easy steps\n\n"
}

function download_latest_files() {
    if [ -d "$webroot_dir" ]; then
        sudo mv $webroot_dir "$webroot_dir.`date +%F-%R`" || install_error "Unable to remove old webroot directory"
    fi

    install_log "Cloning latest files from github"
	rm -Rf /tmp/RaspHotspotwebgui
    git clone https://github.com/LeprovostNoam/RaspHotspot /tmp/RaspHotspotwebgui || install_error "Unable to download files from github"
    sudo mv /tmp/RaspHotspotwebgui $webroot_dir || install_error "Unable to move RaspHotspot to web root" 
	chmod +x $webroot_dir/_inc/idents.php
	chmod +x $webroot_dir/_inc/version.php
	chmod +x $webroot_dir/_inc/version.txt
}


display_welcome
download_latest_files
install_log "update completed"