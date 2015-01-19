NagVis backend installation
============================

Download NagVis 1.7.x archive and extract it in */usr/local/nagvis*
  
Configure vhosts so that /nagvis URL are available.

You can use the template provided by NagVis, on CES, it is stored it in */etc/httpd/conf.d/11-nagvis.conf*, close to *10-centreon.conf* (comments have been removed in the file below):

::

  Alias /nagvis "/usr/local/nagvis/share" 
  
  <Directory "/usr/local/nagvis/share">
    Options FollowSymLinks
    AllowOverride None
    Order allow,deny
    Allow from all
  
    <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteBase /nagvis
  
      RewriteCond %{REQUEST_URI} ^/nagvis/frontend/(wui|nagvis-js)
      RewriteCond %{QUERY_STRING} map=(.*)
      RewriteRule ^(.*)$ /nagvis/frontend/nagvis-js/index.php?mod=Map&act=view&show=%1 [R=301,L]
  
      RewriteCond %{REQUEST_URI} ^/nagvis/frontend(/wui)?/?(index.php)?$
      RewriteRule ^(.*)$ /nagvis/frontend/nagvis-js/index.php [R=301,L]
  
      RewriteCond %{REQUEST_URI} ^/nagvis/frontend/nagvis-js
      RewriteCond %{QUERY_STRING} !mod
      RewriteCond %{QUERY_STRING} rotation=(.*)
      RewriteRule ^(.*)$ /nagvis/frontend/nagvis-js/index.php?mod=Rotation&act=view&show=%1 [R=301,L]
    </IfModule>
  </Directory>

Clone the Broker backend provided by this forge:

::

  # cd /tmp
  # git clone http://<login>:git.centreon.com/centreon-nagvis-backend.git
  [...]
  # cd centreon-nagvis-backend
  # cp GlobalBackendcentreonbroker.php /usr/local/nagvis/share/server/core/classes/

Create some directories needed by NagVis:

::

  # mkdir etc/profiles
  # mkdir -p /var/tmpl/cache/
  # mkdir -p /var/tmpl/compile/
  # chown -R apache.apache /usr/local/nagvis

.. note::
  At this stage, you should be able to connect to NagVis using http://ip_of_centreon_server/nagvis, and admin/admin account in order to modify NagVis configuration.
  We will describe textual configuration in this documentation.

Edit */usr/local/nagvis/nagvis.ini.php* (comments have been removed in the file below):

::

  [global]
  authmodule="CoreAuthModSQLite"
  authorisationmodule="CoreAuthorisationModSQLite" 
  dateformat="Y-m-d H:i:s" 
  file_group="apache" 
  file_mode="660" 
  language_detection="user,session,browser,config" 
  language="en_US" 
  refreshtime=60
  sesscookiedomain="auto-detect" 
  sesscookiepath="/" 
  sesscookieduration="86400" 
  startmodule="Overview" 
  startaction="view" 
  
  [paths]
  base="/usr/local/nagvis/" 
  htmlbase="/nagvis" 
  htmlcgi="centreon/main.php" 
  
  [defaults]
  backend="centreonbroker" 
  backgroundcolor="#ffffff" 
  contextmenu=1
  contexttemplate="default" 
  event_on_load=0
  event_repeat_interval=0
  event_repeat_duration=-1
  eventbackground=0
  eventhighlight=1
  eventhighlightduration=30000
  eventhighlightinterval=500
  eventlog=0
  eventloghidden="1" 
  eventscroll=1
  headermenu="1" 
  headertemplate="default" 
  headerfade=1
  hovermenu=1
  hovertemplate="default" 
  hoverdelay=0
  hoverchildsshow=1
  hoverchildslimit="10" 
  hoverchildsorder="asc" 
  hoverchildssort="s" 
  icons="std_medium" 
  onlyhardstates=0
  recognizeservices=1
  showinlists=1
  showinmultisite=1
  urltarget="_parent" 
  hosturl="[htmlcgi]/main.php?p=20201&o=svc&host_search=[host_name]&search=&poller=&hostgroup=&output_search=" 
  hostgroupurl=
  serviceurl="[htmlcgi]/main.php?p=20201&o=svcd&host_name=[host_name]&service_description=[service_description]&poller=&hostgroup=&output_search=" 
  servicegroupurl=
  mapurl="[htmlcgi]/main.php?p=403&map=[map_name]" 
  view_template="default" 
  label_show=1
  
  [index]
  backgroundcolor=#ffffff
  cellsperrow=4
  headermenu="1" 
  headertemplate="default" 
  showmaps=1
  showgeomap=0
  showrotations=1
  showmapthumbs=0
  
  [automap]
  
  [wui]
  maplocktime=5
  grid_show=0
  grid_color="#D5DCEF" 
  grid_steps=32
  
  [worker]
  interval=10
  requestmaxparams=0
  requestmaxlength=1900
  updateobjectstates=30
  
  [backend_centreonbroker]
  backendtype="centreonbroker" 
  statushost="" 
  dbhost="localhost" 
  dbport=3306
  dbname="centreon_storage" 
  dbuser="centreon" 
  dbpass="centreon" 
  dbinstancename="default" 
  htmlcgi="/centreon" 
  
  [states]

The important directives here are:

* Broker backend definition
* associated credentials to access centreon_storage
* Centreon URL so that a user consulting hosts and services in NagVis can be redirected to the associated object in Centreon ; please note that it is currently NOT possible to redirect a user to a view of a host group or service group as Centreon do not have a dedicated URL at the moment

Now, you should be able to create new maps in NagVis, and use drag and drop to add hosts and services available in Centreon.

Centreon objects should appear in NagVis

.. image:: _static/centreon_integration.png

When displaying maps, you should get this kind of status popup (here on a service):

.. image:: _static/result1.png

you get the same kind op popup on a map:

.. image:: _static/result2.png

