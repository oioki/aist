# aist
Statistics for Asterisk
<img src="https://cloud.githubusercontent.com/assets/1127549/9838393/083f8454-5a5d-11e5-92e9-8b4e68a11b60.png"/>

Installation
------------
* Clone from repository:
```
git clone --depth 1 https://github.com/oioki/aist.git
```
* Install libraries (Smarty, jQuery etc...)
```
./fetch-libs.sh
```
* Customize configs/app.conf for your Asterisk installation.
* Your CDR scheme should be at least like this:
```
CREATE TABLE `cdr` (
  `calldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `src` varchar(80) NOT NULL DEFAULT '',
  `dst` varchar(80) NOT NULL DEFAULT '',
  `duration` int(11) NOT NULL DEFAULT '0',
  `billsec` int(11) NOT NULL DEFAULT '0',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `accountcode` varchar(20) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
```
