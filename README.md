# `$ RaspHotspot` [![Version 1.18.4.21](https://img.shields.io/badge/Version-1.18.4.21-green.svg)](https://github.com/LeprovostNoam/RaspHotspot/)
RaspHotspot is a free responsive wifi hotspot control interface for raspberry pi

![](https://i.imgur.com/Vyh5T1F.png)
![](https://i.imgur.com/F4mkbxs.png)
![](https://i.imgur.com/TocF5Lj.png)


## Contents

 - [Prerequisites](#prerequisites)
 - [Quick installer](#quick-installer)
 - [Manual installation](#manual-installation)
 - [Optional services](#optional-services)
 - [How to contribute](#how-to-contribute)
 - [License](#license)
 
 ## Prerequisites
 To use RaspHotspot, you must have a raspberry pi with the raspbian operating system installed on it.
 ## Quick installer
 Install RaspHotspotfrom your RaspberryPi's terminal:
```sh
$ wget -q https://git.io/vpYXV -O /tmp/RaspHotspot && bash /tmp/RaspHotspot
```
The installer will complete the steps in the manual installation (below) for you.

After the reboot at the end of the installation the wireless network will be
configured as an access point as follows:
* IP address: 10.3.141.1
  * Username: admin
  * Password: admin
* DHCP range: 10.3.141.50 to 10.3.141.255
* SSID: `RaspHotspot`
* Password: password




contact: noam@leprovost.pro
