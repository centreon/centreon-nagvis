User guide
==========

Check default NagVis location in *Administration > Nagvis* (*/usr/local/nagvis/share*).

Add a new "centreon_nagvis" user in NagVis administration (this will populate */usr/local/nagvis/etc/auth.db* accordingly, which is the SQLite DB used by NagVis).
Grant him the "Users (read only)" role.

.. image:: ../_static/centreon_nagvis.png

Edit */usr/local/nagvis/share/server/core/defines/global.php*:

::

  # Default value
  # define('SESSION_NAME', 'nagvis_session');
  define('SESSION_NAME', 'PHPSESSID');

This is needed to "share" cookie with Centreon.

You should now be able to display NagVis maps in Centreon.

.. warning:: 
  Once integrated in Centreon, there is this light SSO between Centreon and NagVis using "centreon_nagvis" NagVis user.

  If you want to edit maps, you'll need to access directly *http://ip_of_centreon_server/nagvis*, log out as centreon_nagvis, log in as admin then edit/create maps.

  Make sure you click on *Edit Map > Lock/Unlock all* to switch in edit mode, and ensure you do the same once map are finished to leave editing mode.

  This will ensure you do NOT see maps in edit mode in Centreon (popup do not appear on mouse over).
